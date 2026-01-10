<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\StorageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {
    
    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Tokens (Public)
    Route::get('/tokens', [TokenController::class, 'index']);
    Route::get('/tokens/trending', [TokenController::class, 'trending']);
    Route::get('/tokens/gainers', [TokenController::class, 'gainers']);
    Route::get('/tokens/losers', [TokenController::class, 'losers']);
    Route::get('/tokens/search', [TokenController::class, 'search']);
    Route::get('/tokens/symbol/{symbol}', [TokenController::class, 'bySymbol']);
    Route::get('/tokens/{id}', [TokenController::class, 'show']);
    Route::get('/tokens/{id}/history', [TokenController::class, 'priceHistory']);
    Route::post('/tokens/compare', [TokenController::class, 'compare']);
    Route::get('/market/stats', [TokenController::class, 'marketStats']);
    
    // Posts (Public)
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    
    // Chatbot (Public - guest access)
    Route::post('/chatbot/conversation', [ChatbotController::class, 'createConversation']);
    Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage']);
    Route::get('/chatbot/conversation/{sessionId}', [ChatbotController::class, 'getConversation']);
    Route::get('/chatbot/suggestions', [ChatbotController::class, 'getSuggestions']);
    
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/password', [AuthController::class, 'changePassword']);
    
    // Posts (Protected)
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{id}/like', [PostController::class, 'like']);
    Route::delete('/posts/{id}/like', [PostController::class, 'unlike']);
    Route::post('/posts/{id}/bookmark', [PostController::class, 'bookmark']);
    Route::delete('/posts/{id}/bookmark', [PostController::class, 'unbookmark']);
    Route::get('/posts/bookmarked/me', [PostController::class, 'bookmarked']);
    
    // Chatbot (Protected)
    Route::get('/chatbot/conversations', [ChatbotController::class, 'getUserConversations']);
    Route::delete('/chatbot/conversation/{sessionId}', [ChatbotController::class, 'deleteConversation']);
    Route::post('/chatbot/message/{messageId}/rate', [ChatbotController::class, 'rateMessage']);
    
    // Cloud Storage (Protected)
    Route::post('/storage/upload', [StorageController::class, 'upload']);
    Route::post('/storage/upload-multiple', [StorageController::class, 'uploadMultiple']);
    Route::post('/storage/upload-url', [StorageController::class, 'uploadFromUrl']);
    Route::post('/storage/upload-base64', [StorageController::class, 'uploadBase64']);
    Route::delete('/storage/delete', [StorageController::class, 'delete']);
    Route::post('/storage/url', [StorageController::class, 'getUrl']);
    Route::get('/storage/files', [StorageController::class, 'listFiles']);
    Route::get('/storage/stats', [StorageController::class, 'getStats']);
    Route::get('/storage/test', [StorageController::class, 'testConnection']);
    
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version' => '1.0.0',
        'features' => [
            'authentication' => true,
            'tokens' => true,
            'community' => true,
            'chatbot' => true,
            'cloud_storage' => true,
        ],
    ]);
});
