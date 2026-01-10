<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'title',
        'context',
        'metadata',
        'is_active',
        'last_message_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('last_message_at');
    }

    // Helper Methods
    public function addMessage(string $role, string $content, array $metadata = [])
    {
        $message = $this->messages()->create([
            'role' => $role,
            'content' => $content,
            'metadata' => $metadata,
        ]);

        $this->update(['last_message_at' => now()]);

        return $message;
    }

    public function getMessageHistory(int $limit = 10)
    {
        return $this->messages()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public function generateTitle()
    {
        $firstMessage = $this->messages()
            ->where('role', 'user')
            ->first();

        if ($firstMessage) {
            $title = substr($firstMessage->content, 0, 50);
            if (strlen($firstMessage->content) > 50) {
                $title .= '...';
            }
            $this->update(['title' => $title]);
        }
    }

    public function getTotalCost()
    {
        return $this->messages()->sum('cost');
    }

    public function getTotalTokens()
    {
        return [
            'input' => $this->messages()->sum('input_tokens'),
            'output' => $this->messages()->sum('output_tokens'),
        ];
    }
}
