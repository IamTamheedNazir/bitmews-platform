<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Token extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'symbol',
        'slug',
        'blockchain_id',
        'contract_address',
        'logo_url',
        'description',
        'website',
        'social_links',
        'category',
        'tags',
        'current_price',
        'market_cap',
        'volume_24h',
        'circulating_supply',
        'total_supply',
        'max_supply',
        'price_change_24h',
        'price_change_7d',
        'market_cap_rank',
        'risk_score',
        'is_verified',
        'is_featured',
        'is_active',
        'views_count',
        'listed_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'tags' => 'array',
        'current_price' => 'decimal:8',
        'market_cap' => 'decimal:2',
        'volume_24h' => 'decimal:2',
        'circulating_supply' => 'decimal:2',
        'total_supply' => 'decimal:2',
        'max_supply' => 'decimal:2',
        'price_change_24h' => 'decimal:2',
        'price_change_7d' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'listed_at' => 'datetime',
    ];

    // Relationships
    public function blockchain()
    {
        return $this->belongsTo(Blockchain::class);
    }

    public function priceHistory()
    {
        return $this->hasMany(TokenPriceHistory::class);
    }

    public function posts()
    {
        return $this->morphToMany(CommunityPost::class, 'taggable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByMarketCap($query)
    {
        return $query->orderBy('market_cap_rank', 'asc');
    }

    public function scopeTrending($query)
    {
        return $query->orderBy('views_count', 'desc')
            ->where('created_at', '>=', now()->subDays(7));
    }

    // Helper Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getPriceChangePercentage($period = '24h')
    {
        return match($period) {
            '24h' => $this->price_change_24h,
            '7d' => $this->price_change_7d,
            default => 0,
        };
    }

    public function isPositiveChange($period = '24h')
    {
        return $this->getPriceChangePercentage($period) > 0;
    }

    public function getMarketCapFormatted()
    {
        return $this->formatLargeNumber($this->market_cap);
    }

    public function getVolumeFormatted()
    {
        return $this->formatLargeNumber($this->volume_24h);
    }

    private function formatLargeNumber($number)
    {
        if ($number >= 1000000000) {
            return '$' . number_format($number / 1000000000, 2) . 'B';
        } elseif ($number >= 1000000) {
            return '$' . number_format($number / 1000000, 2) . 'M';
        } elseif ($number >= 1000) {
            return '$' . number_format($number / 1000, 2) . 'K';
        }
        return '$' . number_format($number, 2);
    }
}
