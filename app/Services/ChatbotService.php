<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Token;
use Illuminate\Support\Str;

class ChatbotService
{
    protected $aiService;
    protected $blockchainService;

    public function __construct(AIService $aiService, BlockchainService $blockchainService)
    {
        $this->aiService = $aiService;
        $this->blockchainService = $blockchainService;
    }

    /**
     * Create new conversation
     */
    public function createConversation($userId = null, string $context = 'general')
    {
        return ChatConversation::create([
            'user_id' => $userId,
            'session_id' => Str::uuid(),
            'context' => $context,
            'is_active' => true,
            'last_message_at' => now(),
        ]);
    }

    /**
     * Get or create conversation
     */
    public function getOrCreateConversation(string $sessionId, $userId = null)
    {
        $conversation = ChatConversation::where('session_id', $sessionId)->first();

        if (!$conversation) {
            $conversation = $this->createConversation($userId);
        }

        return $conversation;
    }

    /**
     * Send message and get AI response
     */
    public function sendMessage(ChatConversation $conversation, string $message)
    {
        // Save user message
        $userMessage = $conversation->addMessage('user', $message);

        // Detect intent and context
        $intent = $this->detectIntent($message);
        $context = $this->buildContext($conversation, $intent);

        // Generate system prompt based on intent
        $systemPrompt = $this->getSystemPrompt($intent);

        // Get conversation history
        $history = $conversation->getMessageHistory(10);

        // Build messages for AI
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        }

        // Get AI response
        $startTime = microtime(true);
        
        try {
            $aiResponse = $this->aiService->process('chatbot', $message, [
                'messages' => $messages,
                'context' => $context,
                'max_tokens' => 1000,
            ]);

            $latency = (microtime(true) - $startTime) * 1000;

            // Save assistant message
            $assistantMessage = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $aiResponse['content'],
                'ai_provider' => $aiResponse['provider'] ?? 'unknown',
                'model_used' => $aiResponse['model'] ?? 'unknown',
                'input_tokens' => $aiResponse['input_tokens'] ?? 0,
                'output_tokens' => $aiResponse['output_tokens'] ?? 0,
                'cost' => $aiResponse['cost'] ?? 0,
                'latency_ms' => round($latency),
            ]);

            // Generate title if first message
            if ($conversation->messages()->count() === 2) {
                $conversation->generateTitle();
            }

            return [
                'success' => true,
                'message' => $assistantMessage,
                'conversation' => $conversation,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Detect user intent from message
     */
    private function detectIntent(string $message): string
    {
        $message = strtolower($message);

        // Token price queries
        if (preg_match('/price|cost|worth|value/i', $message)) {
            return 'token_price';
        }

        // Token information
        if (preg_match('/what is|tell me about|info|information/i', $message)) {
            return 'token_info';
        }

        // Market analysis
        if (preg_match('/market|trend|analysis|bullish|bearish/i', $message)) {
            return 'market_analysis';
        }

        // Trading advice
        if (preg_match('/buy|sell|trade|invest|should i/i', $message)) {
            return 'trading_advice';
        }

        // News
        if (preg_match('/news|latest|update|happening/i', $message)) {
            return 'news';
        }

        return 'general';
    }

    /**
     * Build context for AI
     */
    private function buildContext(ChatConversation $conversation, string $intent): array
    {
        $context = [
            'intent' => $intent,
            'conversation_context' => $conversation->context,
        ];

        // Add relevant data based on intent
        if ($intent === 'token_price' || $intent === 'token_info') {
            // Get trending tokens
            $tokens = Token::active()->trending()->limit(5)->get();
            $context['trending_tokens'] = $tokens->map(function($token) {
                return [
                    'name' => $token->name,
                    'symbol' => $token->symbol,
                    'price' => $token->current_price,
                    'change_24h' => $token->price_change_24h,
                ];
            });
        }

        if ($intent === 'market_analysis') {
            // Get market stats
            $context['market_stats'] = $this->blockchainService->getGlobalMarketData();
        }

        return $context;
    }

    /**
     * Get system prompt based on intent
     */
    private function getSystemPrompt(string $intent): string
    {
        $basePrompt = "You are BitMews AI Assistant, a helpful crypto intelligence chatbot. You provide accurate, up-to-date information about cryptocurrencies, market trends, and blockchain technology. ";

        $intentPrompts = [
            'token_price' => "Focus on providing current token prices, market cap, and price changes. Use the provided trending tokens data.",
            'token_info' => "Provide detailed information about cryptocurrencies including their purpose, technology, and use cases.",
            'market_analysis' => "Analyze market trends, sentiment, and provide insights based on current market data.",
            'trading_advice' => "Provide educational trading insights. Always remind users that this is not financial advice and they should do their own research (DYOR).",
            'news' => "Share latest crypto news and updates. Focus on significant market events and developments.",
            'general' => "Answer general questions about crypto, blockchain, and the BitMews platform.",
        ];

        return $basePrompt . ($intentPrompts[$intent] ?? $intentPrompts['general']) . 
               "\n\nBe concise, friendly, and helpful. Use emojis sparingly. If you don't know something, admit it.";
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory($userId, int $limit = 20)
    {
        return ChatConversation::where('user_id', $userId)
            ->active()
            ->recent()
            ->with(['messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->limit($limit)
            ->get();
    }

    /**
     * Delete conversation
     */
    public function deleteConversation(ChatConversation $conversation)
    {
        $conversation->update(['is_active' => false]);
        return true;
    }

    /**
     * Get suggested prompts
     */
    public function getSuggestedPrompts(string $category = 'general')
    {
        return [
            'general' => [
                ['icon' => '💰', 'title' => 'Token Prices', 'prompt' => 'What are the current prices of top cryptocurrencies?'],
                ['icon' => '📊', 'title' => 'Market Analysis', 'prompt' => 'Give me a market analysis of Bitcoin'],
                ['icon' => '📰', 'title' => 'Latest News', 'prompt' => 'What are the latest crypto news?'],
                ['icon' => '🎓', 'title' => 'Learn Crypto', 'prompt' => 'Explain blockchain technology to me'],
            ],
            'token' => [
                ['icon' => '🔍', 'title' => 'Token Info', 'prompt' => 'Tell me about Ethereum'],
                ['icon' => '📈', 'title' => 'Price Trend', 'prompt' => 'What is the price trend of BTC?'],
                ['icon' => '⚖️', 'title' => 'Compare', 'prompt' => 'Compare Bitcoin and Ethereum'],
                ['icon' => '💎', 'title' => 'Hidden Gems', 'prompt' => 'What are some promising altcoins?'],
            ],
            'trading' => [
                ['icon' => '📊', 'title' => 'Technical Analysis', 'prompt' => 'Analyze BTC chart'],
                ['icon' => '🎯', 'title' => 'Entry Points', 'prompt' => 'When should I buy Bitcoin?'],
                ['icon' => '⚠️', 'title' => 'Risk Management', 'prompt' => 'How to manage trading risks?'],
                ['icon' => '📚', 'title' => 'Trading Tips', 'prompt' => 'Give me crypto trading tips'],
            ],
        ];
    }
}
