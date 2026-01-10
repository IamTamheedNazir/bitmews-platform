<?php

namespace App\Services;

use App\Models\AIProvider;
use App\Models\AIUseCase;
use App\Models\AIUsageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Process AI request with automatic fallback
     */
    public function process(string $useCaseSlug, string $prompt, array $options = [])
    {
        $useCase = AIUseCase::where('slug', $useCaseSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $provider = $useCase->getProvider();
        
        if (!$provider) {
            throw new \Exception('No active AI provider available');
        }

        $startTime = microtime(true);
        
        try {
            $response = $this->callProvider($provider, $prompt, $options);
            $latency = (microtime(true) - $startTime) * 1000;
            
            $this->logUsage($provider, $useCase, $response, $latency, true);
            
            return $response;
            
        } catch (\Exception $e) {
            $latency = (microtime(true) - $startTime) * 1000;
            $this->logUsage($provider, $useCase, [], $latency, false, $e->getMessage());
            
            // Try fallback providers
            $fallbackProviders = $useCase->getFallbackProviders();
            
            foreach ($fallbackProviders as $fallbackProvider) {
                try {
                    $response = $this->callProvider($fallbackProvider, $prompt, $options);
                    $this->logUsage($fallbackProvider, $useCase, $response, $latency, true);
                    return $response;
                } catch (\Exception $fallbackError) {
                    continue;
                }
            }
            
            throw new \Exception('All AI providers failed: ' . $e->getMessage());
        }
    }

    /**
     * Call specific AI provider
     */
    private function callProvider(AIProvider $provider, string $prompt, array $options = [])
    {
        $config = array_merge($provider->config ?? [], $options);
        
        return match($provider->slug) {
            'openai' => $this->callOpenAI($provider, $prompt, $config),
            'gemini' => $this->callGemini($provider, $prompt, $config),
            'claude' => $this->callClaude($provider, $prompt, $config),
            'kimi' => $this->callKimi($provider, $prompt, $config),
            'perplexity' => $this->callPerplexity($provider, $prompt, $config),
            default => throw new \Exception('Unsupported AI provider: ' . $provider->slug),
        };
    }

    /**
     * OpenAI API call
     */
    private function callOpenAI(AIProvider $provider, string $prompt, array $config)
    {
        if (!$provider->api_key) {
            throw new \Exception('OpenAI API key not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $provider->api_key,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($provider->base_url . '/chat/completions', [
            'model' => $config['model'] ?? $provider->models[0],
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $config['temperature'] ?? 0.7,
            'max_tokens' => $config['max_tokens'] ?? 1000,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'content' => $data['choices'][0]['message']['content'],
            'model' => $data['model'],
            'input_tokens' => $data['usage']['prompt_tokens'],
            'output_tokens' => $data['usage']['completion_tokens'],
            'total_tokens' => $data['usage']['total_tokens'],
        ];
    }

    /**
     * Google Gemini API call
     */
    private function callGemini(AIProvider $provider, string $prompt, array $config)
    {
        if (!$provider->api_key) {
            throw new \Exception('Gemini API key not configured');
        }

        $model = $config['model'] ?? $provider->models[0];
        
        $response = Http::timeout(30)->post(
            $provider->base_url . "/v1/models/{$model}:generateContent?key=" . $provider->api_key,
            [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => $config['temperature'] ?? 0.7,
                    'maxOutputTokens' => $config['max_tokens'] ?? 1000,
                ],
            ]
        );

        if (!$response->successful()) {
            throw new \Exception('Gemini API error: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'content' => $data['candidates'][0]['content']['parts'][0]['text'],
            'model' => $model,
            'input_tokens' => $data['usageMetadata']['promptTokenCount'] ?? 0,
            'output_tokens' => $data['usageMetadata']['candidatesTokenCount'] ?? 0,
            'total_tokens' => $data['usageMetadata']['totalTokenCount'] ?? 0,
        ];
    }

    /**
     * Anthropic Claude API call
     */
    private function callClaude(AIProvider $provider, string $prompt, array $config)
    {
        if (!$provider->api_key) {
            throw new \Exception('Claude API key not configured');
        }

        $response = Http::withHeaders([
            'x-api-key' => $provider->api_key,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($provider->base_url . '/v1/messages', [
            'model' => $config['model'] ?? $provider->models[0],
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => $config['max_tokens'] ?? 1000,
            'temperature' => $config['temperature'] ?? 0.7,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Claude API error: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'content' => $data['content'][0]['text'],
            'model' => $data['model'],
            'input_tokens' => $data['usage']['input_tokens'],
            'output_tokens' => $data['usage']['output_tokens'],
            'total_tokens' => $data['usage']['input_tokens'] + $data['usage']['output_tokens'],
        ];
    }

    /**
     * Kimi (Moonshot) API call
     */
    private function callKimi(AIProvider $provider, string $prompt, array $config)
    {
        if (!$provider->api_key) {
            throw new \Exception('Kimi API key not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $provider->api_key,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($provider->base_url . '/chat/completions', [
            'model' => $config['model'] ?? $provider->models[0],
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $config['temperature'] ?? 0.7,
            'max_tokens' => $config['max_tokens'] ?? 1000,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Kimi API error: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'content' => $data['choices'][0]['message']['content'],
            'model' => $data['model'],
            'input_tokens' => $data['usage']['prompt_tokens'],
            'output_tokens' => $data['usage']['completion_tokens'],
            'total_tokens' => $data['usage']['total_tokens'],
        ];
    }

    /**
     * Perplexity API call
     */
    private function callPerplexity(AIProvider $provider, string $prompt, array $config)
    {
        if (!$provider->api_key) {
            throw new \Exception('Perplexity API key not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $provider->api_key,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($provider->base_url . '/chat/completions', [
            'model' => $config['model'] ?? $provider->models[0],
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $config['temperature'] ?? 0.7,
            'max_tokens' => $config['max_tokens'] ?? 1000,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Perplexity API error: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'content' => $data['choices'][0]['message']['content'],
            'model' => $data['model'],
            'input_tokens' => $data['usage']['prompt_tokens'] ?? 0,
            'output_tokens' => $data['usage']['completion_tokens'] ?? 0,
            'total_tokens' => $data['usage']['total_tokens'] ?? 0,
        ];
    }

    /**
     * Log AI usage
     */
    private function logUsage(
        AIProvider $provider,
        AIUseCase $useCase,
        array $response,
        float $latency,
        bool $success,
        ?string $errorMessage = null
    ) {
        $cost = 0;
        
        if ($success && isset($response['input_tokens'], $response['output_tokens'])) {
            $cost = $provider->calculateCost(
                $response['input_tokens'],
                $response['output_tokens']
            );
        }

        AIUsageLog::create([
            'provider_id' => $provider->id,
            'use_case_id' => $useCase->id,
            'model_used' => $response['model'] ?? 'unknown',
            'input_tokens' => $response['input_tokens'] ?? 0,
            'output_tokens' => $response['output_tokens'] ?? 0,
            'cost' => $cost,
            'latency_ms' => round($latency),
            'success' => $success,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Summarize text
     */
    public function summarize(string $text, int $maxLength = 200)
    {
        $prompt = "Summarize the following text in {$maxLength} characters or less:\n\n{$text}";
        
        $response = $this->process('news_summary', $prompt, [
            'max_tokens' => 500,
        ]);
        
        return $response['content'];
    }

    /**
     * Analyze sentiment
     */
    public function analyzeSentiment(string $text)
    {
        $prompt = "Analyze the sentiment of this crypto-related text. Respond with only one word: bullish, bearish, or neutral.\n\n{$text}";
        
        $response = $this->process('sentiment_analysis', $prompt, [
            'max_tokens' => 10,
        ]);
        
        return strtolower(trim($response['content']));
    }

    /**
     * Generate content
     */
    public function generateContent(string $topic, string $type = 'article')
    {
        $prompt = "Write a {$type} about: {$topic}";
        
        $response = $this->process('long_context', $prompt, [
            'max_tokens' => 2000,
        ]);
        
        return $response['content'];
    }
}
