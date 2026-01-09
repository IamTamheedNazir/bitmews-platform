<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'use_case_id',
        'model_used',
        'input_tokens',
        'output_tokens',
        'cost',
        'latency_ms',
        'success',
        'error_message',
    ];

    protected $casts = [
        'cost' => 'decimal:6',
        'success' => 'boolean',
    ];

    // Relationships
    public function provider()
    {
        return $this->belongsTo(AIProvider::class, 'provider_id');
    }

    public function useCase()
    {
        return $this->belongsTo(AIUseCase::class, 'use_case_id');
    }
}
