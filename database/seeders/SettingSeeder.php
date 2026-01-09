<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'BitMews', 'type' => 'string', 'group' => 'general', 'description' => 'Website name', 'is_public' => true],
            ['key' => 'site_tagline', 'value' => 'AI-Powered Crypto Intelligence Platform', 'type' => 'string', 'group' => 'general', 'description' => 'Website tagline', 'is_public' => true],
            ['key' => 'site_description', 'value' => 'Real-time crypto news, AI analysis, multi-chain data, and community insights', 'type' => 'text', 'group' => 'general', 'description' => 'Website description', 'is_public' => true],
            ['key' => 'site_url', 'value' => 'https://bitmews.com', 'type' => 'string', 'group' => 'general', 'description' => 'Website URL', 'is_public' => true],
            ['key' => 'admin_email', 'value' => 'admin@bitmews.com', 'type' => 'string', 'group' => 'general', 'description' => 'Admin email address', 'is_public' => false],
            ['key' => 'contact_email', 'value' => 'contact@bitmews.com', 'type' => 'string', 'group' => 'general', 'description' => 'Contact email', 'is_public' => true],
            ['key' => 'support_email', 'value' => 'support@bitmews.com', 'type' => 'string', 'group' => 'general', 'description' => 'Support email', 'is_public' => true],
            
            // Appearance
            ['key' => 'logo_url', 'value' => '/images/logo.svg', 'type' => 'string', 'group' => 'appearance', 'description' => 'Site logo URL', 'is_public' => true],
            ['key' => 'favicon_url', 'value' => '/images/favicon.ico', 'type' => 'string', 'group' => 'appearance', 'description' => 'Favicon URL', 'is_public' => true],
            ['key' => 'primary_color', 'value' => '#F7931A', 'type' => 'string', 'group' => 'appearance', 'description' => 'Primary brand color (Bitcoin orange)', 'is_public' => true],
            ['key' => 'secondary_color', 'value' => '#0F0F0F', 'type' => 'string', 'group' => 'appearance', 'description' => 'Secondary color (Dark)', 'is_public' => true],
            ['key' => 'theme_mode', 'value' => 'dark', 'type' => 'string', 'group' => 'appearance', 'description' => 'Default theme mode', 'is_public' => true],
            
            // Features
            ['key' => 'registration_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'features', 'description' => 'Allow new user registration', 'is_public' => true],
            ['key' => 'email_verification_required', 'value' => 'true', 'type' => 'boolean', 'group' => 'features', 'description' => 'Require email verification', 'is_public' => false],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'features', 'description' => 'Enable maintenance mode', 'is_public' => true],
            ['key' => 'write_to_earn_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'features', 'description' => 'Enable Write-to-Earn program', 'is_public' => true],
            ['key' => 'earnings_per_point', 'value' => '0.01', 'type' => 'string', 'group' => 'features', 'description' => 'USD per engagement point', 'is_public' => false],
            ['key' => 'max_earnings_per_post', 'value' => '100', 'type' => 'string', 'group' => 'features', 'description' => 'Maximum earnings per post', 'is_public' => false],
            
            // Social Media
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/bitmews', 'type' => 'string', 'group' => 'social', 'description' => 'Twitter/X URL', 'is_public' => true],
            ['key' => 'telegram_url', 'value' => 'https://t.me/bitmews', 'type' => 'string', 'group' => 'social', 'description' => 'Telegram URL', 'is_public' => true],
            ['key' => 'discord_url', 'value' => 'https://discord.gg/bitmews', 'type' => 'string', 'group' => 'social', 'description' => 'Discord URL', 'is_public' => true],
            ['key' => 'github_url', 'value' => 'https://github.com/bitmews', 'type' => 'string', 'group' => 'social', 'description' => 'GitHub URL', 'is_public' => true],
            
            // SEO
            ['key' => 'meta_title', 'value' => 'BitMews - AI-Powered Crypto Intelligence', 'type' => 'string', 'group' => 'seo', 'description' => 'Meta title', 'is_public' => true],
            ['key' => 'meta_description', 'value' => 'Get real-time crypto news, AI-powered analysis, multi-chain data, and community insights all in one platform', 'type' => 'text', 'group' => 'seo', 'description' => 'Meta description', 'is_public' => true],
            ['key' => 'meta_keywords', 'value' => 'crypto, bitcoin, ethereum, blockchain, cryptocurrency news, AI analysis', 'type' => 'text', 'group' => 'seo', 'description' => 'Meta keywords', 'is_public' => true],
            ['key' => 'google_analytics_id', 'value' => '', 'type' => 'string', 'group' => 'seo', 'description' => 'Google Analytics ID', 'is_public' => false],
            ['key' => 'google_tag_manager_id', 'value' => '', 'type' => 'string', 'group' => 'seo', 'description' => 'Google Tag Manager ID', 'is_public' => false],
            
            // Email
            ['key' => 'mail_from_name', 'value' => 'BitMews', 'type' => 'string', 'group' => 'email', 'description' => 'Email from name', 'is_public' => false],
            ['key' => 'mail_from_address', 'value' => 'noreply@bitmews.com', 'type' => 'string', 'group' => 'email', 'description' => 'Email from address', 'is_public' => false],
            
            // API
            ['key' => 'api_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'api', 'description' => 'Enable API access', 'is_public' => true],
            ['key' => 'api_rate_limit_default', 'value' => '100', 'type' => 'string', 'group' => 'api', 'description' => 'Default API rate limit per day', 'is_public' => false],
            
            // Security
            ['key' => 'two_factor_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'security', 'description' => 'Enable 2FA', 'is_public' => false],
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'string', 'group' => 'security', 'description' => 'Minimum password length', 'is_public' => false],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'string', 'group' => 'security', 'description' => 'Session lifetime in minutes', 'is_public' => false],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'string', 'group' => 'security', 'description' => 'Max login attempts before lockout', 'is_public' => false],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
