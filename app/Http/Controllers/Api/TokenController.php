<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Services\BlockchainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TokenController extends Controller
{
    protected $blockchainService;

    public function __construct(BlockchainService $blockchainService)
    {
        $this->blockchainService = $blockchainService;
    }

    /**
     * Get all tokens
     */
    public function index(Request $request)
    {
        $query = Token::with(['blockchain'])
            ->active();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        // Filter by blockchain
        if ($request->has('blockchain_id')) {
            $query->where('blockchain_id', $request->blockchain_id);
        }

        // Filter by verified
        if ($request->has('verified')) {
            $query->where('is_verified', $request->boolean('verified'));
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'market_cap_rank');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if ($sortBy === 'market_cap_rank') {
            $query->orderBy('market_cap_rank', $sortOrder);
        } elseif ($sortBy === 'price') {
            $query->orderBy('current_price', $sortOrder);
        } elseif ($sortBy === 'volume') {
            $query->orderBy('volume_24h', $sortOrder);
        } elseif ($sortBy === 'change') {
            $query->orderBy('price_change_24h', $sortOrder);
        }

        $perPage = $request->get('per_page', 20);
        $tokens = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    /**
     * Get single token
     */
    public function show($id)
    {
        $token = Token::with(['blockchain', 'priceHistory'])
            ->findOrFail($id);

        // Increment views
        $token->incrementViews();

        // Fetch latest price
        $this->blockchainService->fetchTokenPrice($token);

        return response()->json([
            'success' => true,
            'data' => $token,
        ]);
    }

    /**
     * Get token price history
     */
    public function priceHistory($id, Request $request)
    {
        $token = Token::findOrFail($id);
        $days = $request->get('days', 7);

        $history = $this->blockchainService->getTokenHistory($token, $days);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    /**
     * Get trending tokens
     */
    public function trending()
    {
        $tokens = Token::active()
            ->trending()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    /**
     * Get top gainers
     */
    public function gainers()
    {
        $tokens = Token::active()
            ->where('price_change_24h', '>', 0)
            ->orderByDesc('price_change_24h')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    /**
     * Get top losers
     */
    public function losers()
    {
        $tokens = Token::active()
            ->where('price_change_24h', '<', 0)
            ->orderBy('price_change_24h')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    /**
     * Search tokens
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = $request->q;
        
        $tokens = Token::active()
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('symbol', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    /**
     * Get token by symbol
     */
    public function bySymbol($symbol)
    {
        $token = Token::where('symbol', strtoupper($symbol))
            ->active()
            ->firstOrFail();

        // Increment views
        $token->incrementViews();

        // Fetch latest price
        $this->blockchainService->fetchTokenPrice($token);

        return response()->json([
            'success' => true,
            'data' => $token,
        ]);
    }

    /**
     * Compare tokens
     */
    public function compare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tokens' => 'required|array|min:2|max:5',
            'tokens.*' => 'required|exists:tokens,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tokens = Token::whereIn('id', $request->tokens)
            ->with('blockchain')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    /**
     * Get global market stats
     */
    public function marketStats()
    {
        $stats = $this->blockchainService->getGlobalMarketData();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
