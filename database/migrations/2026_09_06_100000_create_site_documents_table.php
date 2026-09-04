<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standalone documents published on the site.
     *
     * Separate from site_post_files: those belong to a story, these are filed
     * under a category and read as a register — procurement notices, job
     * adverts, annual reports.
     */
    public function up(): void
    {
        Schema::create('site_documents', function (Blueprint $table) {
            $table->id();

            $table->string('category', 30);
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('file_path');
            $table->string('file_original_name');
            $table->string('file_mime')->nullable();
            $table->string('file_extension', 20)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);

            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedBigInteger('download_count')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_documents');
    }
};
