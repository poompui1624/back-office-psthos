<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_rooms', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('location')->nullable();
            $table->integer('capacity')->default(0);

            $table->boolean('has_projector')->default(false);
            $table->boolean('has_sound_system')->default(false);
            $table->boolean('has_video_conference')->default(false);
            $table->boolean('has_whiteboard')->default(false);

            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_rooms');
    }
};
