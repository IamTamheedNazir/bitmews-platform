<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blockchain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'symbol',
        'type',
        'logo_url',
        'description',
        'rpc_url',
        'explorer_url',
        'chain_id',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLayer1($query)
    {
        return $query->where('type', 'layer1');
    }

    public function scopeLayer2($query)
    {
        return $query->where('type', 'layer2');
    }

    // Helper Methods
    public function isLayer1()
    {
        return $this->type === 'layer1';
    }

    public function isLayer2()
    {
        return $this->type === 'layer2';
    }

    public function getExplorerLink($address)
    {
        return $this->explorer_url . '/address/' . $address;
    }

    public function getTransactionLink($txHash)
    {
        return $this->explorer_url . '/tx/' . $txHash;
    }
}
