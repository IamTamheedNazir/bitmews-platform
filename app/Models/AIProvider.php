<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'api_key',
        'base_url',
        'models',
        'config',
        'priority',
        'cost_per_1k_input',
        'cost_per_1k_output',
        'rate_limit_per_minute',
        'max_tokens',
        'is_active',
    ];

    protected $casts = [
        'models' => 'array',
        'config' => 'array',
        'cost_per_1k_input' => 'decimal:6',
        'cost_per_1k_output' => 'decimal:6',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function useCases()
    {
        return $this->hasMany(AIUseCase::class, 'primary_provider_id');
    }

    public function usageLogs()
    {
        return $this->hasMany(AIUsageLog::class, 'provider_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function calculateCost($inputTokens, $outputTokens)
    {
        $inputCost = ($inputTokens / 1000) * $this->cost_per_1k_input;
        $outputCost = ($outputTokens / 1000) * $this->cost_per_1k_output;
        return $inputCost + $outputCost;
    }

    public function getTotalCost()
    {
        return $this->usageLogs()->sum('cost');
    }

    public function getTotalTokens()
    {
        return [
            'input' => $this->usageLogs()->sum('input_tokens'),
            'output' => $this->usageLogs()->sum('output_tokens'),
        ];
    }

    public function getAverageLatency()
    {
        return $this->usageLogs()->avg('latency_ms');
    }

    public function getSuccessRate()
    {
        $total = $this->usageLogs()->count();
        if ($total === 0) return 100;
        
        $successful = $this->usageLogs()->where('success', true)->count();
        return ($successful / $total) * 100;
    }
}
