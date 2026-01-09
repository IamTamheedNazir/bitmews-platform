<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'avatar',
        'bio',
        'phone',
        'country',
        'timezone',
        'language',
        'currency',
        'is_active',
        'is_verified',
        'two_factor_enabled',
        'two_factor_secret',
        'balance',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'balance' => 'decimal:2',
        'last_login_at' => 'datetime',
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_members')
            ->withPivot('role', 'permissions', 'joined_at')
            ->withTimestamps();
    }

    public function ownedOrganizations()
    {
        return $this->hasMany(Organization::class, 'owner_id');
    }

    public function posts()
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function earnings()
    {
        return $this->hasMany(CreatorEarning::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'community_follows', 'following_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'community_follows', 'follower_id', 'following_id');
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    // Helper Methods
    public function hasRole($role)
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasPermission($permission)
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('slug', $permission);
        })->exists();
    }

    public function isAdmin()
    {
        return $this->hasRole('super-admin') || $this->hasRole('admin');
    }

    public function isSubscribed()
    {
        return $this->activeSubscription()->exists();
    }

    public function getSubscriptionPlan()
    {
        return $this->activeSubscription?->plan;
    }

    public function canAccessFeature($feature)
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return false;
        }
        return in_array($feature, $subscription->plan->features);
    }

    public function incrementFollowers()
    {
        $this->increment('followers_count');
    }

    public function decrementFollowers()
    {
        $this->decrement('followers_count');
    }

    public function incrementFollowing()
    {
        $this->increment('following_count');
    }

    public function decrementFollowing()
    {
        $this->decrement('following_count');
    }
}
