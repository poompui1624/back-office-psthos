<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * News, activities, and knowledge articles for the public site.
     */
    public function up(): void
    {
        Schema::create('site_posts', function (Blueprint $table) {
            $table->id();

            $table->string('category', 30);

            $table->string('title');

            // Used in the public URL, so a shared link stays readable. Thai
            // titles keep their characters rather than collapsing to an empty
            // slug the way a latin-only slugger would leave them.
            $table->string('slug')->unique();

            $table->string('excerpt', 500)->nullable();
            $table->text('body')->nullable();
            $table->string('cover_image_path')->nullable();

            // Set to the future to schedule; null means never published.
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_pinned')->default(false);

            $table->unsignedBigInteger('view_count')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'is_published', 'published_at']);
            $table->index(['is_pinned', 'published_at']);
        });

        Schema::create('site_post_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_post_id')->constrained('site_posts')->cascadeOnDelete();

            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['site_post_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_post_images');
        Schema::dropIfExists('site_posts');
    }
};
