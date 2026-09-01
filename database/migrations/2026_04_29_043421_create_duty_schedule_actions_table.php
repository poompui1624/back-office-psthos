<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_schedule_actions', function (Blueprint $table) {
            $table->id();

            // The constraint is added by a later migration: duty_schedules is
            // created after this table, so declaring it here fails on a fresh
            // database.
            $table->foreignId('duty_schedule_id');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action', 50);

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->text('remark')->nullable();

            $table->timestamps();

            $table->index('duty_schedule_id');
            $table->index('user_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_schedule_actions');
    }
};
