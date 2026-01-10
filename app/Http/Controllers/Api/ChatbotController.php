<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Create new conversation
     */
    public function createConversation(Request $request)
    {
        $conversation = $this->chatbotService->createConversation(
            $request->user()?->id,
            $request->get('context', 'general')
        );

        return response()->json([
            'success' => true,
            'data' => $conversation,
        ], 201);
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $conversation = $this->chatbotService->getOrCreateConversation(
            $request->session_id,
            $request->user()?->id
        );

        $result = $this->chatbotService->sendMessage(
            $conversation,
            $request->message
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $result['message'],
                'conversation' => $result['conversation'],
            ],
        ]);
    }

    /**
     * Get conversation history
     */
    public function getConversation($sessionId)
    {
        $conversation = ChatConversation::where('session_id', $sessionId)
            ->with('messages')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $conversation,
        ]);
    }

    /**
     * Get user conversations
     */
    public function getUserConversations(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        $conversations = $this->chatbotService->getConversationHistory(
            $request->user()->id,
            $request->get('limit', 20)
        );

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Delete conversation
     */
    public function deleteConversation(Request $request, $sessionId)
    {
        $conversation = ChatConversation::where('session_id', $sessionId)
            ->firstOrFail();

        // Check ownership
        if ($conversation->user_id && $conversation->user_id !== $request->user()?->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $this->chatbotService->deleteConversation($conversation);

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted',
        ]);
    }

    /**
     * Rate message
     */
    public function rateMessage(Request $request, $messageId)
    {
        $validator = Validator::make($request->all(), [
            'helpful' => 'required|boolean',
            'feedback' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $message = \App\Models\ChatMessage::findOrFail($messageId);
        
        $message->markAsHelpful(
            $request->helpful,
            $request->feedback
        );

        return response()->json([
            'success' => true,
            'message' => 'Feedback recorded',
        ]);
    }

    /**
     * Get suggested prompts
     */
    public function getSuggestions(Request $request)
    {
        $category = $request->get('category', 'general');
        $suggestions = $this->chatbotService->getSuggestedPrompts($category);

        return response()->json([
            'success' => true,
            'data' => $suggestions[$category] ?? $suggestions['general'],
        ]);
    }
}
