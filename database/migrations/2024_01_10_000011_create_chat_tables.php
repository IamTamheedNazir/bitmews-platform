<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->string('title')->nullable();
            $table->string('context')->default('general'); // general, token, news, trading
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index('session_id');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->text('content');
            $table->string('ai_provider')->nullable(); // openai, gemini, claude, etc.
            $table->string('model_used')->nullable();
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->integer('latency_ms')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_helpful')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            
            $table->index('conversation_id');
            $table->index('role');
        });

        Schema::create('chat_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // token, news, trading, general
            $table->string('title');
            $table->text('prompt');
            $table->string('icon')->nullable();
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
        Schema::dropIfExists('chat_suggestions');
    }
};
