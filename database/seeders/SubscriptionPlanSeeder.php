<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for getting started with crypto intelligence',
                'price' => 0,
                'currency' => 'USD',
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'Basic news access',
                    'Limited token data',
                    '100 API calls/day',
                    'Community access',
                    'Email support',
                ],
                'api_rate_limit' => 100,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Essential tools for crypto enthusiasts',
                'price' => 9.99,
                'currency' => 'USD',
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'Full news access',
                    'Real-time token data',
                    '1,000 API calls/day',
                    'Price alerts',
                    'Portfolio tracking (5 wallets)',
                    'AI-powered insights',
                    'Priority email support',
                ],
                'api_rate_limit' => 1000,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Advanced features for serious traders',
                'price' => 29.99,
                'currency' => 'USD',
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'Everything in Basic',
                    '10,000 API calls/day',
                    'Advanced analytics',
                    'Portfolio tracking (unlimited wallets)',
                    'Risk scoring & scam detection',
                    'Custom alerts',
                    'Multi-chain support',
                    'Write-to-Earn eligibility',
                    'Priority support',
                    'Ad-free experience',
                ],
                'api_rate_limit' => 10000,
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Expert',
                'slug' => 'expert',
                'description' => 'Complete platform access for professionals',
                'price' => 99.99,
                'currency' => 'USD',
                'interval' => 'monthly',
                'interval_count' => 1,
                'features' => [
                    'Everything in Pro',
                    'Unlimited API calls',
                    'White-label API access',
                    'Custom integrations',
                    'Dedicated account manager',
                    'Early access to new features',
                    'Organization accounts',
                    'Team collaboration',
                    'Advanced reporting',
                    'Custom branding',
                    '24/7 priority support',
                ],
                'api_rate_limit' => 999999,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
