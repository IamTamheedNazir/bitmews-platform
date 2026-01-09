<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'type',
        'decimals',
        'exchange_rate',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFiat($query)
    {
        return $query->where('type', 'fiat');
    }

    public function scopeCrypto($query)
    {
        return $query->where('type', 'crypto');
    }

    // Helper Methods
    public function isFiat()
    {
        return $this->type === 'fiat';
    }

    public function isCrypto()
    {
        return $this->type === 'crypto';
    }

    public function convert($amount, $toCurrency)
    {
        if (is_string($toCurrency)) {
            $toCurrency = self::where('code', $toCurrency)->firstOrFail();
        }
        
        // Convert to USD first, then to target currency
        $usdAmount = $amount / $this->exchange_rate;
        return $usdAmount * $toCurrency->exchange_rate;
    }

    public function format($amount)
    {
        return $this->symbol . number_format($amount, $this->decimals);
    }
}
