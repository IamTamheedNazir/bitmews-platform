<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blockchains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('symbol', 10);
            $table->string('type'); // layer1, layer2, sidechain
            $table->string('logo_url')->nullable();
            $table->text('description')->nullable();
            $table->string('rpc_url')->nullable();
            $table->string('explorer_url')->nullable();
            $table->integer('chain_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });

        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol', 20)->unique();
            $table->string('slug')->unique();
            $table->foreignId('blockchain_id')->constrained()->onDelete('cascade');
            $table->string('contract_address')->nullable();
            $table->string('logo_url')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->decimal('current_price', 20, 8)->nullable();
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->decimal('volume_24h', 20, 2)->nullable();
            $table->decimal('circulating_supply', 30, 2)->nullable();
            $table->decimal('total_supply', 30, 2)->nullable();
            $table->decimal('max_supply', 30, 2)->nullable();
            $table->decimal('price_change_24h', 10, 2)->nullable();
            $table->decimal('price_change_7d', 10, 2)->nullable();
            $table->integer('market_cap_rank')->nullable();
            $table->integer('risk_score')->default(50); // 0-100
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->timestamp('listed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['symbol', 'is_active']);
            $table->index(['blockchain_id', 'is_active']);
            $table->index('market_cap_rank');
            $table->fullText(['name', 'symbol', 'description']);
        });

        Schema::create('token_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 20, 8);
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->decimal('volume', 20, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            $table->index(['token_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_price_history');
        Schema::dropIfExists('tokens');
        Schema::dropIfExists('blockchains');
    }
};
