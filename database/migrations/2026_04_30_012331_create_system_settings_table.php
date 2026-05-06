<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            $table->string('group', 100)->default('general');
            $table->string('key', 150)->unique();

            $table->string('label');
            $table->text('value')->nullable();
            $table->string('type', 50)->default('text');
            $table->text('description')->nullable();

            $table->json('options')->nullable();

            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('group');
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
