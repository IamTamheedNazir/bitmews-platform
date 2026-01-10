<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'interval',
        'interval_count',
        'features',
        'api_rate_limit',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function activeSubscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id')->where('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    // Helper Methods
    public function isFree()
    {
        return $this->price == 0;
    }

    public function hasFeature($feature)
    {
        return in_array($feature, $this->features);
    }

    public function getMonthlyPrice()
    {
        if ($this->interval === 'monthly') {
            return $this->price;
        }
        
        if ($this->interval === 'yearly') {
            return $this->price / 12;
        }
        
        return $this->price;
    }

    public function getYearlyPrice()
    {
        if ($this->interval === 'yearly') {
            return $this->price;
        }
        
        if ($this->interval === 'monthly') {
            return $this->price * 12;
        }
        
        return $this->price * 12;
    }

    public function getSavingsPercentage()
    {
        if ($this->interval !== 'yearly') {
            return 0;
        }
        
        $monthlyTotal = $this->getMonthlyPrice() * 12;
        $savings = $monthlyTotal - $this->price;
        
        return round(($savings / $monthlyTotal) * 100);
    }
}
