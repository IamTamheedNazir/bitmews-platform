<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('interval'); // monthly, yearly
            $table->integer('interval_count')->default(1);
            $table->json('features');
            $table->integer('api_rate_limit')->default(1000);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans');
            $table->string('status'); // active, canceled, expired, past_due
            $table->string('stripe_subscription_id')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });

        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('feature');
            $table->integer('usage')->default(0);
            $table->integer('limit')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();
            
            $table->index(['subscription_id', 'feature']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usage');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
