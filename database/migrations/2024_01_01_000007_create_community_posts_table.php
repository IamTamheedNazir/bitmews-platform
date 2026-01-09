<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('post'); // post, article, video, poll, trade_idea
            $table->string('title')->nullable();
            $table->longText('content');
            $table->json('media')->nullable();
            $table->json('tags')->nullable();
            $table->json('token_tags')->nullable();
            $table->json('metadata')->nullable();
            $table->string('sentiment')->nullable(); // bullish, bearish, neutral
            $table->boolean('is_sponsored')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('bookmarks_count')->default(0);
            $table->decimal('earnings', 10, 2)->default(0);
            $table->string('status')->default('published'); // draft, published, moderation, rejected
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['type', 'published_at']);
            $table->index(['status', 'published_at']);
            $table->fullText(['title', 'content']);
        });

        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('community_comments')->onDelete('cascade');
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['post_id', 'created_at']);
        });

        Schema::create('community_likes', function (Blueprint $table) {
            $table->id();
            $table->morphs('likeable');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['likeable_type', 'likeable_id', 'user_id']);
        });

        Schema::create('community_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'post_id']);
        });

        Schema::create('community_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['follower_id', 'following_id']);
        });

        Schema::create('creator_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->nullable()->constrained('community_posts')->onDelete('set null');
            $table->string('type'); // write_to_earn, tips, sponsored, affiliate
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_earnings');
        Schema::dropIfExists('community_follows');
        Schema::dropIfExists('community_bookmarks');
        Schema::dropIfExists('community_likes');
        Schema::dropIfExists('community_comments');
        Schema::dropIfExists('community_posts');
    }
};
