<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIUseCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'primary_provider_id',
        'fallback_providers',
        'config',
        'is_active',
    ];

    protected $casts = [
        'fallback_providers' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function primaryProvider()
    {
        return $this->belongsTo(AIProvider::class, 'primary_provider_id');
    }

    public function usageLogs()
    {
        return $this->hasMany(AIUsageLog::class, 'use_case_id');
    }

    // Helper Methods
    public function getFallbackProviders()
    {
        if (empty($this->fallback_providers)) {
            return collect();
        }
        
        return AIProvider::whereIn('id', $this->fallback_providers)
            ->where('is_active', true)
            ->orderByRaw('FIELD(id, ' . implode(',', $this->fallback_providers) . ')')
            ->get();
    }

    public function getProvider()
    {
        if ($this->primaryProvider && $this->primaryProvider->is_active) {
            return $this->primaryProvider;
        }
        
        return $this->getFallbackProviders()->first();
    }
}
