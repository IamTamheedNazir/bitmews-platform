<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'logo_url',
        'supported_currencies',
        'supported_methods',
        'supported_countries',
        'credentials',
        'config',
        'transaction_fee_percent',
        'transaction_fee_fixed',
        'priority',
        'is_active',
        'is_test_mode',
    ];

    protected $casts = [
        'supported_currencies' => 'array',
        'supported_methods' => 'array',
        'supported_countries' => 'array',
        'config' => 'array',
        'transaction_fee_percent' => 'decimal:2',
        'transaction_fee_fixed' => 'decimal:2',
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
    ];

    // Relationships
    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'gateway_id');
    }

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

    public function scopeSupportsCurrency($query, $currency)
    {
        return $query->whereJsonContains('supported_currencies', $currency);
    }

    // Helper Methods
    public function supportsCurrency($currency)
    {
        return in_array($currency, $this->supported_currencies);
    }

    public function supportsCountry($country)
    {
        if (in_array('*', $this->supported_countries ?? [])) {
            return true;
        }
        return in_array($country, $this->supported_countries ?? []);
    }

    public function calculateFee($amount)
    {
        $percentFee = ($amount * $this->transaction_fee_percent) / 100;
        return $percentFee + $this->transaction_fee_fixed;
    }

    public function getNetAmount($amount)
    {
        return $amount - $this->calculateFee($amount);
    }
}
