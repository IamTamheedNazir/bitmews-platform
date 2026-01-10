<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'ai_provider',
        'model_used',
        'input_tokens',
        'output_tokens',
        'cost',
        'latency_ms',
        'metadata',
        'is_helpful',
        'feedback',
    ];

    protected $casts = [
        'metadata' => 'array',
        'cost' => 'decimal:6',
        'is_helpful' => 'boolean',
    ];

    // Relationships
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    // Helper Methods
    public function markAsHelpful(bool $helpful, ?string $feedback = null)
    {
        $this->update([
            'is_helpful' => $helpful,
            'feedback' => $feedback,
        ]);
    }

    public function isFromUser()
    {
        return $this->role === 'user';
    }

    public function isFromAssistant()
    {
        return $this->role === 'assistant';
    }
}
