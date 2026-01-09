<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('api_key')->nullable(); // encrypted
            $table->string('base_url')->nullable();
            $table->json('models');
            $table->json('config')->nullable();
            $table->integer('priority')->default(0);
            $table->decimal('cost_per_1k_input', 10, 6)->default(0);
            $table->decimal('cost_per_1k_output', 10, 6)->default(0);
            $table->integer('rate_limit_per_minute')->default(60);
            $table->integer('max_tokens')->default(4096);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['is_active', 'priority']);
        });

        Schema::create('ai_use_cases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('primary_provider_id')->constrained('ai_providers');
            $table->json('fallback_providers')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('ai_providers');
            $table->foreignId('use_case_id')->constrained('ai_use_cases');
            $table->string('model_used');
            $table->integer('input_tokens');
            $table->integer('output_tokens');
            $table->decimal('cost', 10, 6);
            $table->integer('latency_ms');
            $table->boolean('success');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['provider_id', 'created_at']);
            $table->index(['use_case_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('ai_use_cases');
        Schema::dropIfExists('ai_providers');
    }
};
