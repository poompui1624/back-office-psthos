<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_daily_summaries', function (Blueprint $table) {
            $table->foreignId('duty_schedule_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('duty_schedules')
                ->nullOnDelete();

            $table->index('duty_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_daily_summaries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('duty_schedule_id');
        });
    }
};
