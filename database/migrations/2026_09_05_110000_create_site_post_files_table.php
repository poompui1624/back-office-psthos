<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Documents attached to a post.
     *
     * Kept apart from site_post_images because the two are read differently: a
     * gallery image is looked at, an attachment is downloaded, and a list that
     * mixes them serves neither.
     */
    public function up(): void
    {
        Schema::create('site_post_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_post_id')->constrained('site_posts')->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->string('file_path');
            $table->string('file_original_name');
            $table->string('file_mime')->nullable();
            $table->string('file_extension', 20)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);

            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('download_count')->default(0);

            $table->timestamps();

            $table->index(['site_post_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_post_files');
    }
};
