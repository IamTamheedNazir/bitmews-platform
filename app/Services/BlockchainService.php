<?php

namespace App\Services;

use App\Models\Token;
use App\Models\TokenPriceHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BlockchainService
{
    /**
     * Fetch token price from CoinGecko
     */
    public function fetchTokenPrice(Token $token)
    {
        $cacheKey = "token_price_{$token->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($token) {
            try {
                $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids' => $token->slug,
                    'vs_currencies' => 'usd',
                    'include_market_cap' => 'true',
                    'include_24hr_vol' => 'true',
                    'include_24hr_change' => 'true',
                ]);

                if ($response->successful() && isset($response->json()[$token->slug])) {
                    $data = $response->json()[$token->slug];
                    
                    $token->update([
                        'current_price' => $data['usd'],
                        'market_cap' => $data['usd_market_cap'] ?? null,
                        'volume_24h' => $data['usd_24h_vol'] ?? null,
                        'price_change_24h' => $data['usd_24h_change'] ?? null,
                    ]);

                    // Store price history
                    TokenPriceHistory::create([
                        'token_id' => $token->id,
                        'price' => $data['usd'],
                        'market_cap' => $data['usd_market_cap'] ?? null,
                        'volume' => $data['usd_24h_vol'] ?? null,
                        'recorded_at' => now(),
                    ]);

                    return $data;
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch token price: ' . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Fetch multiple token prices
     */
    public function fetchMultipleTokenPrices(array $tokenIds)
    {
        $tokens = Token::whereIn('id', $tokenIds)->get();
        $slugs = $tokens->pluck('slug')->implode(',');

        try {
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => $slugs,
                'vs_currencies' => 'usd',
                'include_market_cap' => 'true',
                'include_24hr_vol' => 'true',
                'include_24hr_change' => 'true',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                foreach ($tokens as $token) {
                    if (isset($data[$token->slug])) {
                        $priceData = $data[$token->slug];
                        
                        $token->update([
                            'current_price' => $priceData['usd'],
                            'market_cap' => $priceData['usd_market_cap'] ?? null,
                            'volume_24h' => $priceData['usd_24h_vol'] ?? null,
                            'price_change_24h' => $priceData['usd_24h_change'] ?? null,
                        ]);
                    }
                }

                return $data;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch multiple token prices: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Get token historical data
     */
    public function getTokenHistory(Token $token, int $days = 7)
    {
        $cacheKey = "token_history_{$token->id}_{$days}";
        
        return Cache::remember($cacheKey, 3600, function () use ($token, $days) {
            try {
                $response = Http::get("https://api.coingecko.com/api/v3/coins/{$token->slug}/market_chart", [
                    'vs_currency' => 'usd',
                    'days' => $days,
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch token history: ' . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Get trending tokens
     */
    public function getTrendingTokens()
    {
        return Cache::remember('trending_tokens', 1800, function () {
            try {
                $response = Http::get('https://api.coingecko.com/api/v3/search/trending');

                if ($response->successful()) {
                    return $response->json()['coins'];
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch trending tokens: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Get global crypto market data
     */
    public function getGlobalMarketData()
    {
        return Cache::remember('global_market_data', 600, function () {
            try {
                $response = Http::get('https://api.coingecko.com/api/v3/global');

                if ($response->successful()) {
                    return $response->json()['data'];
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch global market data: ' . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Verify smart contract on blockchain
     */
    public function verifyContract(Token $token)
    {
        if (!$token->contract_address) {
            return false;
        }

        $blockchain = $token->blockchain;
        
        if (!$blockchain->rpc_url) {
            return false;
        }

        try {
            // Check if contract exists
            $response = Http::post($blockchain->rpc_url, [
                'jsonrpc' => '2.0',
                'method' => 'eth_getCode',
                'params' => [$token->contract_address, 'latest'],
                'id' => 1,
            ]);

            if ($response->successful()) {
                $code = $response->json()['result'];
                return $code !== '0x' && $code !== '0x0';
            }
        } catch (\Exception $e) {
            \Log::error('Failed to verify contract: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Get token holders count
     */
    public function getTokenHolders(Token $token)
    {
        if (!$token->contract_address) {
            return 0;
        }

        $cacheKey = "token_holders_{$token->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($token) {
            // This would integrate with blockchain explorers like Etherscan
            // For now, return a placeholder
            return 0;
        });
    }

    /**
     * Calculate token risk score
     */
    public function calculateRiskScore(Token $token)
    {
        $score = 50; // Start with neutral score

        // Verified contract
        if ($token->is_verified) {
            $score -= 10;
        }

        // Has liquidity
        if ($token->volume_24h > 100000) {
            $score -= 10;
        }

        // Market cap
        if ($token->market_cap > 10000000) {
            $score -= 10;
        }

        // Age of token
        if ($token->listed_at && $token->listed_at->diffInDays(now()) > 365) {
            $score -= 10;
        }

        // Price volatility
        if (abs($token->price_change_24h) > 20) {
            $score += 15;
        }

        return max(0, min(100, $score));
    }

    /**
     * Update all token prices (for cron job)
     */
    public function updateAllTokenPrices()
    {
        $tokens = Token::active()->get();
        $chunks = $tokens->chunk(50); // Process in batches

        foreach ($chunks as $chunk) {
            $this->fetchMultipleTokenPrices($chunk->pluck('id')->toArray());
            sleep(1); // Rate limiting
        }
    }
}
