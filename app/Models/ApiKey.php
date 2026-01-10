<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'key',
        'secret',
        'permissions',
        'rate_limit',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected $hidden = [
        'secret',
    ];

    protected $casts = [
        'permissions' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    // Helper Methods
    public function updateLastUsed()
    {
        $this->update(['last_used_at' => now()]);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public static function generateKey()
    {
        return 'bm_' . bin2hex(random_bytes(32));
    }

    public static function generateSecret()
    {
        return bin2hex(random_bytes(32));
    }
}
