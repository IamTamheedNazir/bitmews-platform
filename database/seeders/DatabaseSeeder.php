<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CurrencySeeder::class,
            PaymentGatewaySeeder::class,
            AIProviderSeeder::class,
            BlockchainSeeder::class,
            SubscriptionPlanSeeder::class,
            SettingSeeder::class,
            
            // Sample data (optional)
            TokenSeeder::class,
            UserSeeder::class,
            CommunityPostSeeder::class,
        ]);
    }
}
