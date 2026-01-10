<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'stripe_subscription_id',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function usage()
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Helper Methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCanceled()
    {
        return $this->status === 'canceled';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function onGracePeriod()
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    public function cancel()
    {
        $this->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'ends_at' => $this->current_period_end,
        ]);
    }

    public function resume()
    {
        $this->update([
            'status' => 'active',
            'canceled_at' => null,
            'ends_at' => null,
        ]);
    }

    public function renew()
    {
        $this->update([
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
            'status' => 'active',
        ]);
    }
}
