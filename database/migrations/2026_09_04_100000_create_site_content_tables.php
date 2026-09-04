<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Content for the hospital's public site, edited from the back-office.
     */
    public function up(): void
    {
        Schema::create('site_banners', function (Blueprint $table) {
            $table->id();

            $table->string('image_path');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('link_url')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            // A banner for a one-off event can be scheduled and expire on its
            // own rather than being remembered and taken down by hand. Both
            // null means it shows for as long as it is active.
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index(['starts_at', 'ends_at']);
        });

        Schema::create('site_links', function (Blueprint $table) {
            $table->id();

            $table->string('label');
            $table->string('url');
            $table->string('icon', 50)->nullable();
            $table->string('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('opens_new_tab')->default(true);

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('site_pages', function (Blueprint $table) {
            $table->id();

            // A fixed set seeded up front — history, vision, structure — so an
            // editor changes what is there rather than creating pages the
            // homepage does not know how to place.
            $table->string('key', 50)->unique();

            $table->string('title');
            $table->text('body')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::create('site_executives', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('position')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            // The one shown prominently on the homepage. Only one may hold it;
            // that is enforced on save, not left to the form.
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_executives');
        Schema::dropIfExists('site_pages');
        Schema::dropIfExists('site_links');
        Schema::dropIfExists('site_banners');
    }
};
