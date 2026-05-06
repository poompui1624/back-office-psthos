<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_no')->unique();

            $table->foreignId('meeting_room_id')
                ->constrained('meeting_rooms')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->text('purpose')->nullable();

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->integer('attendees_count')->default(0);

            $table->boolean('need_projector')->default(false);
            $table->boolean('need_sound_system')->default(false);
            $table->boolean('need_video_conference')->default(false);
            $table->boolean('need_whiteboard')->default(false);

            $table->string('status', 50)->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('rejected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('rejected_at')->nullable();

            $table->timestamp('cancelled_at')->nullable();

            $table->text('approval_remark')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('booking_no');
            $table->index('meeting_room_id');
            $table->index('employee_id');
            $table->index('department_id');
            $table->index('start_at');
            $table->index('end_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_bookings');
    }
};
