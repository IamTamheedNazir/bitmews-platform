<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'content',
        'media',
        'tags',
        'token_tags',
        'metadata',
        'sentiment',
        'is_sponsored',
        'is_premium',
        'views_count',
        'likes_count',
        'comments_count',
        'shares_count',
        'bookmarks_count',
        'earnings',
        'status',
        'published_at',
    ];

    protected $casts = [
        'media' => 'array',
        'tags' => 'array',
        'token_tags' => 'array',
        'metadata' => 'array',
        'is_sponsored' => 'boolean',
        'is_premium' => 'boolean',
        'earnings' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class, 'post_id');
    }

    public function likes()
    {
        return $this->morphMany(CommunityLike::class, 'likeable');
    }

    public function bookmarks()
    {
        return $this->hasMany(CommunityBookmark::class, 'post_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeTrending($query)
    {
        return $query->published()
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('views_count');
    }

    public function scopeBySentiment($query, $sentiment)
    {
        return $query->where('sentiment', $sentiment);
    }

    // Helper Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    public function decrementLikes()
    {
        $this->decrement('likes_count');
    }

    public function incrementComments()
    {
        $this->increment('comments_count');
    }

    public function decrementComments()
    {
        $this->decrement('comments_count');
    }

    public function incrementShares()
    {
        $this->increment('shares_count');
    }

    public function incrementBookmarks()
    {
        $this->increment('bookmarks_count');
    }

    public function decrementBookmarks()
    {
        $this->decrement('bookmarks_count');
    }

    public function isLikedBy(User $user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isBookmarkedBy(User $user)
    {
        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function getReadingTime()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // Average reading speed
        return $minutes . ' min read';
    }
}
