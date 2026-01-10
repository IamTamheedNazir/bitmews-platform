<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\PostController;

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
    
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version' => '1.0.0',
    ]);
});
