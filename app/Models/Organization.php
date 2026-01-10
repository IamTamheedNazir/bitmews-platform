<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'email',
        'logo_url',
        'description',
        'website',
        'social_links',
        'country',
        'type',
        'is_verified',
        'is_active',
        'verification_documents',
        'verified_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'verification_documents' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'organization_members')
            ->withPivot('role', 'permissions', 'joined_at')
            ->withTimestamps();
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
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

    // Helper Methods
    public function addMember(User $user, $role = 'member')
    {
        $this->members()->attach($user->id, [
            'role' => $role,
            'joined_at' => now(),
        ]);
    }

    public function removeMember(User $user)
    {
        $this->members()->detach($user->id);
    }

    public function isOwner(User $user)
    {
        return $this->owner_id === $user->id;
    }

    public function isMember(User $user)
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function getMemberRole(User $user)
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        return $member?->pivot->role;
    }
}
