<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Currencies
    |--------------------------------------------------------------------------
    |
    | List of all supported currencies (fiat and crypto) in the platform.
    | Format: 'CODE' => ['name' => 'Name', 'symbol' => 'Symbol', 'type' => 'fiat|crypto']
    |
    */

    'fiat' => [
        'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'decimals' => 2],
        'EUR' => ['name' => 'Euro', 'symbol' => '€', 'decimals' => 2],
        'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'decimals' => 2],
        'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹', 'decimals' => 2],
        'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥', 'decimals' => 0],
        'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥', 'decimals' => 2],
        'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$', 'decimals' => 2],
        'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$', 'decimals' => 2],
        'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'CHF', 'decimals' => 2],
        'SEK' => ['name' => 'Swedish Krona', 'symbol' => 'kr', 'decimals' => 2],
        'NZD' => ['name' => 'New Zealand Dollar', 'symbol' => 'NZ$', 'decimals' => 2],
        'MXN' => ['name' => 'Mexican Peso', 'symbol' => 'MX$', 'decimals' => 2],
        'SGD' => ['name' => 'Singapore Dollar', 'symbol' => 'S$', 'decimals' => 2],
        'HKD' => ['name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'decimals' => 2],
        'NOK' => ['name' => 'Norwegian Krone', 'symbol' => 'kr', 'decimals' => 2],
        'KRW' => ['name' => 'South Korean Won', 'symbol' => '₩', 'decimals' => 0],
        'TRY' => ['name' => 'Turkish Lira', 'symbol' => '₺', 'decimals' => 2],
        'RUB' => ['name' => 'Russian Ruble', 'symbol' => '₽', 'decimals' => 2],
        'BRL' => ['name' => 'Brazilian Real', 'symbol' => 'R$', 'decimals' => 2],
        'ZAR' => ['name' => 'South African Rand', 'symbol' => 'R', 'decimals' => 2],
        'DKK' => ['name' => 'Danish Krone', 'symbol' => 'kr', 'decimals' => 2],
        'PLN' => ['name' => 'Polish Zloty', 'symbol' => 'zł', 'decimals' => 2],
        'THB' => ['name' => 'Thai Baht', 'symbol' => '฿', 'decimals' => 2],
        'IDR' => ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'decimals' => 0],
        'HUF' => ['name' => 'Hungarian Forint', 'symbol' => 'Ft', 'decimals' => 0],
        'CZK' => ['name' => 'Czech Koruna', 'symbol' => 'Kč', 'decimals' => 2],
        'ILS' => ['name' => 'Israeli Shekel', 'symbol' => '₪', 'decimals' => 2],
        'CLP' => ['name' => 'Chilean Peso', 'symbol' => 'CLP$', 'decimals' => 0],
        'PHP' => ['name' => 'Philippine Peso', 'symbol' => '₱', 'decimals' => 2],
        'AED' => ['name' => 'UAE Dirham', 'symbol' => 'د.إ', 'decimals' => 2],
        'SAR' => ['name' => 'Saudi Riyal', 'symbol' => '﷼', 'decimals' => 2],
        'MYR' => ['name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'decimals' => 2],
        'RON' => ['name' => 'Romanian Leu', 'symbol' => 'lei', 'decimals' => 2],
    ],

    'crypto' => [
        'BTC' => ['name' => 'Bitcoin', 'symbol' => '₿', 'decimals' => 8],
        'ETH' => ['name' => 'Ethereum', 'symbol' => 'Ξ', 'decimals' => 18],
        'USDT' => ['name' => 'Tether', 'symbol' => '₮', 'decimals' => 6],
        'USDC' => ['name' => 'USD Coin', 'symbol' => 'USDC', 'decimals' => 6],
        'BNB' => ['name' => 'Binance Coin', 'symbol' => 'BNB', 'decimals' => 18],
        'XRP' => ['name' => 'Ripple', 'symbol' => 'XRP', 'decimals' => 6],
        'ADA' => ['name' => 'Cardano', 'symbol' => 'ADA', 'decimals' => 6],
        'SOL' => ['name' => 'Solana', 'symbol' => 'SOL', 'decimals' => 9],
        'DOGE' => ['name' => 'Dogecoin', 'symbol' => 'Ð', 'decimals' => 8],
        'DOT' => ['name' => 'Polkadot', 'symbol' => 'DOT', 'decimals' => 10],
        'MATIC' => ['name' => 'Polygon', 'symbol' => 'MATIC', 'decimals' => 18],
        'AVAX' => ['name' => 'Avalanche', 'symbol' => 'AVAX', 'decimals' => 18],
        'LINK' => ['name' => 'Chainlink', 'symbol' => 'LINK', 'decimals' => 18],
        'UNI' => ['name' => 'Uniswap', 'symbol' => 'UNI', 'decimals' => 18],
        'ATOM' => ['name' => 'Cosmos', 'symbol' => 'ATOM', 'decimals' => 6],
        'LTC' => ['name' => 'Litecoin', 'symbol' => 'Ł', 'decimals' => 8],
        'BCH' => ['name' => 'Bitcoin Cash', 'symbol' => 'BCH', 'decimals' => 8],
        'NEAR' => ['name' => 'Near Protocol', 'symbol' => 'NEAR', 'decimals' => 24],
        'APT' => ['name' => 'Aptos', 'symbol' => 'APT', 'decimals' => 8],
        'SUI' => ['name' => 'Sui', 'symbol' => 'SUI', 'decimals' => 9],
    ],

    'default' => env('APP_CURRENCY', 'USD'),
];
