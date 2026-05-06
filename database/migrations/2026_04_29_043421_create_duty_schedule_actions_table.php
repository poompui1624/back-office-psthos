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

            $table->foreignId('duty_schedule_id')
                ->constrained('duty_schedules')
                ->cascadeOnDelete();

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
