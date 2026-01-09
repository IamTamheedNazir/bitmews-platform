<?php

namespace Database\Seeders;

use App\Models\AIProvider;
use App\Models\AIUseCase;
use Illuminate\Database\Seeder;

class AIProviderSeeder extends Seeder
{
    public function run(): void
    {
        // Create AI Providers
        $providers = [
            [
                'name' => 'OpenAI',
                'slug' => 'openai',
                'description' => 'GPT-4, GPT-4o, GPT-4.1 - Best for tool calling and real-time voice',
                'base_url' => 'https://api.openai.com/v1',
                'models' => ['gpt-4o', 'gpt-4-turbo', 'gpt-4', 'gpt-3.5-turbo'],
                'config' => ['temperature' => 0.7, 'max_tokens' => 4096],
                'priority' => 100,
                'cost_per_1k_input' => 0.005,
                'cost_per_1k_output' => 0.015,
                'rate_limit_per_minute' => 60,
                'max_tokens' => 128000,
                'is_active' => true,
            ],
            [
                'name' => 'Google Gemini',
                'slug' => 'gemini',
                'description' => 'Gemini 2.5 Pro, 3.0 Pro - Best for long context (1M+ tokens)',
                'base_url' => 'https://generativelanguage.googleapis.com',
                'models' => ['gemini-2.5-pro', 'gemini-3.0-pro', 'gemini-pro'],
                'config' => ['temperature' => 0.7, 'max_tokens' => 8192],
                'priority' => 95,
                'cost_per_1k_input' => 0.00125,
                'cost_per_1k_output' => 0.005,
                'rate_limit_per_minute' => 60,
                'max_tokens' => 1000000,
                'is_active' => true,
            ],
            [
                'name' => 'Anthropic Claude',
                'slug' => 'claude',
                'description' => 'Claude 4.5 Sonnet/Opus - Best for coding and safety',
                'base_url' => 'https://api.anthropic.com',
                'models' => ['claude-4.5-sonnet', 'claude-4.5-opus', 'claude-3-opus'],
                'config' => ['temperature' => 0.7, 'max_tokens' => 4096],
                'priority' => 90,
                'cost_per_1k_input' => 0.003,
                'cost_per_1k_output' => 0.015,
                'rate_limit_per_minute' => 50,
                'max_tokens' => 200000,
                'is_active' => true,
            ],
            [
                'name' => 'Kimi (Moonshot AI)',
                'slug' => 'kimi',
                'description' => 'Moonshot v1 - Cost-effective, great for Chinese language',
                'base_url' => 'https://api.moonshot.cn/v1',
                'models' => ['moonshot-v1-8k', 'moonshot-v1-32k', 'moonshot-v1-128k'],
                'config' => ['temperature' => 0.7, 'max_tokens' => 4096],
                'priority' => 85,
                'cost_per_1k_input' => 0.001,
                'cost_per_1k_output' => 0.002,
                'rate_limit_per_minute' => 60,
                'max_tokens' => 128000,
                'is_active' => true,
            ],
            [
                'name' => 'Perplexity',
                'slug' => 'perplexity',
                'description' => 'Sonar Pro - Best for research and web search',
                'base_url' => 'https://api.perplexity.ai',
                'models' => ['sonar-pro', 'sonar-reasoning-pro'],
                'config' => ['temperature' => 0.7, 'max_tokens' => 4096],
                'priority' => 80,
                'cost_per_1k_input' => 0.003,
                'cost_per_1k_output' => 0.015,
                'rate_limit_per_minute' => 50,
                'max_tokens' => 127072,
                'is_active' => true,
            ],
        ];

        foreach ($providers as $provider) {
            AIProvider::create($provider);
        }

        // Create Use Cases
        $useCases = [
            [
                'name' => 'News Summarization',
                'slug' => 'news_summary',
                'primary_provider_id' => 1, // OpenAI
                'fallback_providers' => [2, 3], // Gemini, Claude
                'config' => [
                    'system_prompt' => 'You are a crypto news summarizer. Provide concise, accurate summaries.',
                    'max_tokens' => 500,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Sentiment Analysis',
                'slug' => 'sentiment_analysis',
                'primary_provider_id' => 3, // Claude
                'fallback_providers' => [1, 2],
                'config' => [
                    'system_prompt' => 'Analyze sentiment: bullish, bearish, or neutral. Be objective.',
                    'max_tokens' => 50,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Price Prediction',
                'slug' => 'price_prediction',
                'primary_provider_id' => 2, // Gemini
                'fallback_providers' => [3, 1],
                'config' => [
                    'system_prompt' => 'You are a crypto price analyst. Provide data-driven predictions.',
                    'max_tokens' => 200,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Long Context Processing',
                'slug' => 'long_context',
                'primary_provider_id' => 2, // Gemini (1M tokens)
                'fallback_providers' => [3],
                'config' => [
                    'system_prompt' => 'Process and analyze large documents.',
                    'max_tokens' => 2000,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Code Analysis',
                'slug' => 'code_analysis',
                'primary_provider_id' => 3, // Claude
                'fallback_providers' => [1],
                'config' => [
                    'system_prompt' => 'Analyze smart contracts and code for security issues.',
                    'max_tokens' => 1000,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Bulk Processing',
                'slug' => 'bulk_processing',
                'primary_provider_id' => 4, // Kimi (cost-effective)
                'fallback_providers' => [1, 2],
                'config' => [
                    'system_prompt' => 'Process data efficiently.',
                    'max_tokens' => 500,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($useCases as $useCase) {
            AIUseCase::create($useCase);
        }
    }
}
