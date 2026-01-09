<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type'); // fiat, crypto, both
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->json('supported_currencies');
            $table->json('supported_methods')->nullable();
            $table->json('supported_countries')->nullable();
            $table->text('credentials')->nullable(); // encrypted
            $table->json('config')->nullable();
            $table->decimal('transaction_fee_percent', 5, 2)->default(0);
            $table->decimal('transaction_fee_fixed', 10, 2)->default(0);
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_test_mode')->default(false);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gateway_id')->constrained('payment_gateways');
            $table->string('gateway_transaction_id')->nullable();
            $table->string('type'); // subscription, token_listing, ad_campaign, etc.
            $table->morphs('payable');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);
            $table->decimal('gateway_fee', 10, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->string('status'); // pending, completed, failed, refunded
            $table->string('payment_method')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['gateway_id', 'created_at']);
            $table->index('transaction_id');
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gateway_id')->constrained('payment_gateways');
            $table->string('type'); // card, bank_account, crypto_wallet
            $table->string('gateway_payment_method_id');
            $table->json('details');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_gateways');
    }
};
