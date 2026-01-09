<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            // Fiat Currencies
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 1.00, 'priority' => 100],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 0.92, 'priority' => 90],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 0.79, 'priority' => 85],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 83.12, 'priority' => 80],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'type' => 'fiat', 'decimals' => 0, 'exchange_rate' => 149.50, 'priority' => 75],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 7.24, 'priority' => 70],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 1.52, 'priority' => 65],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 1.35, 'priority' => 60],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 0.88, 'priority' => 55],
            ['code' => 'SEK', 'name' => 'Swedish Krona', 'symbol' => 'kr', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 10.35, 'priority' => 50],
            ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 1.63, 'priority' => 45],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => 'MX$', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 17.05, 'priority' => 40],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 1.34, 'priority' => 35],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 7.83, 'priority' => 30],
            ['code' => 'NOK', 'name' => 'Norwegian Krone', 'symbol' => 'kr', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 10.68, 'priority' => 25],
            ['code' => 'KRW', 'name' => 'South Korean Won', 'symbol' => '₩', 'type' => 'fiat', 'decimals' => 0, 'exchange_rate' => 1305.50, 'priority' => 20],
            ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => '₺', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 32.15, 'priority' => 15],
            ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => '₽', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 92.50, 'priority' => 10],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 4.97, 'priority' => 5],
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R', 'type' => 'fiat', 'decimals' => 2, 'exchange_rate' => 18.25, 'priority' => 0],

            // Cryptocurrencies
            ['code' => 'BTC', 'name' => 'Bitcoin', 'symbol' => '₿', 'type' => 'crypto', 'decimals' => 8, 'exchange_rate' => 0.000023, 'priority' => 100],
            ['code' => 'ETH', 'name' => 'Ethereum', 'symbol' => 'Ξ', 'type' => 'crypto', 'decimals' => 18, 'exchange_rate' => 0.00042, 'priority' => 95],
            ['code' => 'USDT', 'name' => 'Tether', 'symbol' => '₮', 'type' => 'crypto', 'decimals' => 6, 'exchange_rate' => 1.00, 'priority' => 90],
            ['code' => 'USDC', 'name' => 'USD Coin', 'symbol' => 'USDC', 'type' => 'crypto', 'decimals' => 6, 'exchange_rate' => 1.00, 'priority' => 85],
            ['code' => 'BNB', 'name' => 'Binance Coin', 'symbol' => 'BNB', 'type' => 'crypto', 'decimals' => 18, 'exchange_rate' => 0.0016, 'priority' => 80],
            ['code' => 'XRP', 'name' => 'Ripple', 'symbol' => 'XRP', 'type' => 'crypto', 'decimals' => 6, 'exchange_rate' => 1.82, 'priority' => 75],
            ['code' => 'ADA', 'name' => 'Cardano', 'symbol' => 'ADA', 'type' => 'crypto', 'decimals' => 6, 'exchange_rate' => 1.15, 'priority' => 70],
            ['code' => 'SOL', 'name' => 'Solana', 'symbol' => 'SOL', 'type' => 'crypto', 'decimals' => 9, 'exchange_rate' => 0.0071, 'priority' => 65],
            ['code' => 'DOGE', 'name' => 'Dogecoin', 'symbol' => 'Ð', 'type' => 'crypto', 'decimals' => 8, 'exchange_rate' => 3.05, 'priority' => 60],
            ['code' => 'DOT', 'name' => 'Polkadot', 'symbol' => 'DOT', 'type' => 'crypto', 'decimals' => 10, 'exchange_rate' => 0.14, 'priority' => 55],
            ['code' => 'MATIC', 'name' => 'Polygon', 'symbol' => 'MATIC', 'type' => 'crypto', 'decimals' => 18, 'exchange_rate' => 1.20, 'priority' => 50],
            ['code' => 'AVAX', 'name' => 'Avalanche', 'symbol' => 'AVAX', 'type' => 'crypto', 'decimals' => 18, 'exchange_rate' => 0.028, 'priority' => 45],
            ['code' => 'LINK', 'name' => 'Chainlink', 'symbol' => 'LINK', 'type' => 'crypto', 'decimals' => 18, 'exchange_rate' => 0.055, 'priority' => 40],
            ['code' => 'UNI', 'name' => 'Uniswap', 'symbol' => 'UNI', 'type' => 'crypto', 'decimals' => 18, 'exchange_rate' => 0.11, 'priority' => 35],
            ['code' => 'ATOM', 'name' => 'Cosmos', 'symbol' => 'ATOM', 'type' => 'crypto', 'decimals' => 6, 'exchange_rate' => 0.12, 'priority' => 30],
            ['code' => 'LTC', 'name' => 'Litecoin', 'symbol' => 'Ł', 'type' => 'crypto', 'decimals' => 8, 'exchange_rate' => 0.011, 'priority' => 25],
            ['code' => 'BCH', 'name' => 'Bitcoin Cash', 'symbol' => 'BCH', 'type' => 'crypto', 'decimals' => 8, 'exchange_rate' => 0.0022, 'priority' => 20],
            ['code' => 'NEAR', 'name' => 'Near Protocol', 'symbol' => 'NEAR', 'type' => 'crypto', 'decimals' => 24, 'exchange_rate' => 0.18, 'priority' => 15],
            ['code' => 'APT', 'name' => 'Aptos', 'symbol' => 'APT', 'type' => 'crypto', 'decimals' => 8, 'exchange_rate' => 0.11, 'priority' => 10],
            ['code' => 'SUI', 'name' => 'Sui', 'symbol' => 'SUI', 'type' => 'crypto', 'decimals' => 9, 'exchange_rate' => 0.23, 'priority' => 5],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
