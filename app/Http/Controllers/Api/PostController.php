<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get all posts
     */
    public function index(Request $request)
    {
        $query = CommunityPost::with(['user'])
            ->published();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by sentiment
        if ($request->has('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        // Filter by tags
        if ($request->has('tags')) {
            $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $query->where(function($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'published_at');
        if ($sortBy === 'trending') {
            $query->trending();
        } elseif ($sortBy === 'popular') {
            $query->orderByDesc('likes_count');
        } else {
            $query->orderByDesc('published_at');
        }

        $perPage = $request->get('per_page', 20);
        $posts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    /**
     * Create new post
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:post,article,video,poll,trade_idea',
            'title' => 'required_if:type,article|string|max:255',
            'content' => 'required|string',
            'media' => 'nullable|array',
            'tags' => 'nullable|array',
            'token_tags' => 'nullable|array',
            'sentiment' => 'nullable|in:bullish,bearish,neutral',
            'is_premium' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Auto-detect sentiment using AI
        $sentiment = $request->sentiment;
        if (!$sentiment) {
            try {
                $sentiment = $this->aiService->analyzeSentiment($request->content);
            } catch (\Exception $e) {
                $sentiment = 'neutral';
            }
        }

        $post = CommunityPost::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'title' => $request->title,
            'content' => $request->content,
            'media' => $request->media,
            'tags' => $request->tags,
            'token_tags' => $request->token_tags,
            'sentiment' => $sentiment,
            'is_premium' => $request->boolean('is_premium'),
            'status' => 'published',
            'published_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post->load('user'),
        ], 201);
    }

    /**
     * Get single post
     */
    public function show($id)
    {
        $post = CommunityPost::with(['user', 'comments.user'])
            ->findOrFail($id);

        // Increment views
        $post->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    /**
     * Update post
     */
    public function update(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        // Check ownership
        if ($post->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'media' => 'nullable|array',
            'tags' => 'nullable|array',
            'sentiment' => 'nullable|in:bullish,bearish,neutral',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $post->update($request->only([
            'title', 'content', 'media', 'tags', 'sentiment'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    /**
     * Delete post
     */
    public function destroy(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        // Check ownership
        if ($post->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);
    }

    /**
     * Like post
     */
    public function like(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        if ($post->isLikedBy($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'Already liked',
            ], 422);
        }

        $post->likes()->create([
            'user_id' => $request->user()->id,
        ]);

        $post->incrementLikes();

        return response()->json([
            'success' => true,
            'message' => 'Post liked',
        ]);
    }

    /**
     * Unlike post
     */
    public function unlike(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        $like = $post->likes()->where('user_id', $request->user()->id)->first();

        if (!$like) {
            return response()->json([
                'success' => false,
                'message' => 'Not liked yet',
            ], 422);
        }

        $like->delete();
        $post->decrementLikes();

        return response()->json([
            'success' => true,
            'message' => 'Post unliked',
        ]);
    }

    /**
     * Bookmark post
     */
    public function bookmark(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        if ($post->isBookmarkedBy($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'Already bookmarked',
            ], 422);
        }

        $post->bookmarks()->create([
            'user_id' => $request->user()->id,
        ]);

        $post->incrementBookmarks();

        return response()->json([
            'success' => true,
            'message' => 'Post bookmarked',
        ]);
    }

    /**
     * Remove bookmark
     */
    public function unbookmark(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);

        $bookmark = $post->bookmarks()->where('user_id', $request->user()->id)->first();

        if (!$bookmark) {
            return response()->json([
                'success' => false,
                'message' => 'Not bookmarked yet',
            ], 422);
        }

        $bookmark->delete();
        $post->decrementBookmarks();

        return response()->json([
            'success' => true,
            'message' => 'Bookmark removed',
        ]);
    }

    /**
     * Get user's bookmarked posts
     */
    public function bookmarked(Request $request)
    {
        $posts = CommunityPost::whereHas('bookmarks', function($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->with('user')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }
}
