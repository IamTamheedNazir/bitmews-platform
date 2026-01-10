<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    /**
     * Create payment transaction
     */
    public function createTransaction(
        User $user,
        PaymentGateway $gateway,
        float $amount,
        string $currency,
        string $type,
        $payable = null
    ) {
        $gatewayFee = $gateway->calculateFee($amount);
        $netAmount = $gateway->getNetAmount($amount);

        return PaymentTransaction::create([
            'transaction_id' => $this->generateTransactionId(),
            'user_id' => $user->id,
            'gateway_id' => $gateway->id,
            'type' => $type,
            'payable_type' => $payable ? get_class($payable) : null,
            'payable_id' => $payable?->id,
            'amount' => $amount,
            'currency' => $currency,
            'gateway_fee' => $gatewayFee,
            'net_amount' => $netAmount,
            'status' => 'pending',
        ]);
    }

    /**
     * Process payment through gateway
     */
    public function processPayment(PaymentTransaction $transaction, array $paymentData = [])
    {
        $gateway = $transaction->gateway;

        try {
            $result = match($gateway->slug) {
                'stripe' => $this->processStripe($transaction, $paymentData),
                'razorpay' => $this->processRazorpay($transaction, $paymentData),
                'paypal' => $this->processPayPal($transaction, $paymentData),
                'coinbase-commerce' => $this->processCoinbase($transaction, $paymentData),
                'cryptomus' => $this->processCryptomus($transaction, $paymentData),
                'nowpayments' => $this->processNOWPayments($transaction, $paymentData),
                default => throw new \Exception('Payment gateway not implemented: ' . $gateway->slug),
            };

            if ($result['success']) {
                $transaction->update([
                    'gateway_transaction_id' => $result['transaction_id'],
                    'status' => $result['status'] ?? 'completed',
                    'payment_method' => $result['payment_method'] ?? null,
                    'metadata' => $result['metadata'] ?? [],
                    'completed_at' => $result['status'] === 'completed' ? now() : null,
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            $transaction->update([
                'status' => 'failed',
                'metadata' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }

    /**
     * Stripe payment processing
     */
    private function processStripe(PaymentTransaction $transaction, array $data)
    {
        $credentials = json_decode($transaction->gateway->credentials, true);
        
        if (!isset($credentials['secret_key'])) {
            throw new \Exception('Stripe secret key not configured');
        }

        // Create Stripe payment intent
        $response = Http::withBasicAuth($credentials['secret_key'], '')
            ->asForm()
            ->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => $transaction->amount * 100, // Convert to cents
                'currency' => strtolower($transaction->currency),
                'payment_method' => $data['payment_method_id'] ?? null,
                'confirm' => true,
                'metadata' => [
                    'transaction_id' => $transaction->transaction_id,
                    'user_id' => $transaction->user_id,
                ],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Stripe payment failed: ' . $response->json()['error']['message']);
        }

        $intent = $response->json();

        return [
            'success' => true,
            'transaction_id' => $intent['id'],
            'status' => $intent['status'] === 'succeeded' ? 'completed' : 'pending',
            'payment_method' => 'card',
            'metadata' => $intent,
        ];
    }

    /**
     * Razorpay payment processing
     */
    private function processRazorpay(PaymentTransaction $transaction, array $data)
    {
        $credentials = json_decode($transaction->gateway->credentials, true);
        
        if (!isset($credentials['key_id'], $credentials['key_secret'])) {
            throw new \Exception('Razorpay credentials not configured');
        }

        // Create Razorpay order
        $response = Http::withBasicAuth($credentials['key_id'], $credentials['key_secret'])
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $transaction->amount * 100, // Convert to paise
                'currency' => $transaction->currency,
                'receipt' => $transaction->transaction_id,
                'notes' => [
                    'user_id' => $transaction->user_id,
                ],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Razorpay order creation failed');
        }

        $order = $response->json();

        return [
            'success' => true,
            'transaction_id' => $order['id'],
            'status' => 'pending',
            'payment_method' => $data['method'] ?? 'upi',
            'metadata' => $order,
        ];
    }

    /**
     * PayPal payment processing
     */
    private function processPayPal(PaymentTransaction $transaction, array $data)
    {
        $credentials = json_decode($transaction->gateway->credentials, true);
        
        if (!isset($credentials['client_id'], $credentials['secret'])) {
            throw new \Exception('PayPal credentials not configured');
        }

        // Get PayPal access token
        $tokenResponse = Http::withBasicAuth($credentials['client_id'], $credentials['secret'])
            ->asForm()
            ->post('https://api-m.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$tokenResponse->successful()) {
            throw new \Exception('PayPal authentication failed');
        }

        $accessToken = $tokenResponse->json()['access_token'];

        // Create PayPal order
        $orderResponse = Http::withToken($accessToken)
            ->post('https://api-m.paypal.com/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $transaction->currency,
                        'value' => number_format($transaction->amount, 2, '.', ''),
                    ],
                    'reference_id' => $transaction->transaction_id,
                ]],
            ]);

        if (!$orderResponse->successful()) {
            throw new \Exception('PayPal order creation failed');
        }

        $order = $orderResponse->json();

        return [
            'success' => true,
            'transaction_id' => $order['id'],
            'status' => 'pending',
            'payment_method' => 'paypal',
            'metadata' => $order,
        ];
    }

    /**
     * Coinbase Commerce crypto payment
     */
    private function processCoinbase(PaymentTransaction $transaction, array $data)
    {
        $credentials = json_decode($transaction->gateway->credentials, true);
        
        if (!isset($credentials['api_key'])) {
            throw new \Exception('Coinbase Commerce API key not configured');
        }

        $response = Http::withHeaders([
            'X-CC-Api-Key' => $credentials['api_key'],
            'X-CC-Version' => '2018-03-22',
        ])->post('https://api.commerce.coinbase.com/charges', [
            'name' => 'BitMews Payment',
            'description' => $transaction->type,
            'pricing_type' => 'fixed_price',
            'local_price' => [
                'amount' => number_format($transaction->amount, 2, '.', ''),
                'currency' => $transaction->currency,
            ],
            'metadata' => [
                'transaction_id' => $transaction->transaction_id,
                'user_id' => $transaction->user_id,
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Coinbase Commerce charge creation failed');
        }

        $charge = $response->json()['data'];

        return [
            'success' => true,
            'transaction_id' => $charge['code'],
            'status' => 'pending',
            'payment_method' => 'crypto',
            'metadata' => $charge,
        ];
    }

    /**
     * Cryptomus crypto payment
     */
    private function processCryptomus(PaymentTransaction $transaction, array $data)
    {
        $credentials = json_decode($transaction->gateway->credentials, true);
        
        if (!isset($credentials['merchant_id'], $credentials['api_key'])) {
            throw new \Exception('Cryptomus credentials not configured');
        }

        $payload = [
            'amount' => number_format($transaction->amount, 2, '.', ''),
            'currency' => $transaction->currency,
            'order_id' => $transaction->transaction_id,
        ];

        $sign = md5(base64_encode(json_encode($payload)) . $credentials['api_key']);

        $response = Http::withHeaders([
            'merchant' => $credentials['merchant_id'],
            'sign' => $sign,
        ])->post('https://api.cryptomus.com/v1/payment', $payload);

        if (!$response->successful()) {
            throw new \Exception('Cryptomus payment creation failed');
        }

        $payment = $response->json()['result'];

        return [
            'success' => true,
            'transaction_id' => $payment['uuid'],
            'status' => 'pending',
            'payment_method' => 'crypto',
            'metadata' => $payment,
        ];
    }

    /**
     * NOWPayments crypto payment
     */
    private function processNOWPayments(PaymentTransaction $transaction, array $data)
    {
        $credentials = json_decode($transaction->gateway->credentials, true);
        
        if (!isset($credentials['api_key'])) {
            throw new \Exception('NOWPayments API key not configured');
        }

        $response = Http::withHeaders([
            'x-api-key' => $credentials['api_key'],
        ])->post('https://api.nowpayments.io/v1/payment', [
            'price_amount' => $transaction->amount,
            'price_currency' => strtolower($transaction->currency),
            'pay_currency' => strtolower($data['crypto_currency'] ?? 'btc'),
            'order_id' => $transaction->transaction_id,
            'order_description' => $transaction->type,
        ]);

        if (!$response->successful()) {
            throw new \Exception('NOWPayments payment creation failed');
        }

        $payment = $response->json();

        return [
            'success' => true,
            'transaction_id' => $payment['payment_id'],
            'status' => 'pending',
            'payment_method' => 'crypto',
            'metadata' => $payment,
        ];
    }

    /**
     * Verify payment webhook
     */
    public function verifyWebhook(string $gatewaySlug, array $payload, string $signature = null)
    {
        $gateway = PaymentGateway::where('slug', $gatewaySlug)->firstOrFail();

        return match($gatewaySlug) {
            'stripe' => $this->verifyStripeWebhook($gateway, $payload, $signature),
            'razorpay' => $this->verifyRazorpayWebhook($gateway, $payload, $signature),
            'coinbase-commerce' => $this->verifyCoinbaseWebhook($gateway, $payload, $signature),
            default => true, // Default to true for other gateways
        };
    }

    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId(): string
    {
        return 'TXN-' . strtoupper(Str::random(16));
    }

    /**
     * Verify Stripe webhook signature
     */
    private function verifyStripeWebhook(PaymentGateway $gateway, array $payload, ?string $signature): bool
    {
        // Implement Stripe webhook verification
        return true;
    }

    /**
     * Verify Razorpay webhook signature
     */
    private function verifyRazorpayWebhook(PaymentGateway $gateway, array $payload, ?string $signature): bool
    {
        // Implement Razorpay webhook verification
        return true;
    }

    /**
     * Verify Coinbase webhook signature
     */
    private function verifyCoinbaseWebhook(PaymentGateway $gateway, array $payload, ?string $signature): bool
    {
        // Implement Coinbase webhook verification
        return true;
    }
}
