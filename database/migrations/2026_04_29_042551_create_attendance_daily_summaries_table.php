<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_daily_summaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->date('work_date');

            $table->dateTime('first_in_at')->nullable();
            $table->dateTime('last_out_at')->nullable();

            $table->time('expected_in_time')->nullable();
            $table->time('expected_out_time')->nullable();

            $table->integer('work_minutes')->default(0);
            $table->integer('late_minutes')->default(0);
            $table->integer('early_leave_minutes')->default(0);

            $table->string('status', 50)->default('normal');
            $table->text('remark')->nullable();

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'work_date'], 'attendance_summary_unique');
            $table->index('employee_id');
            $table->index('work_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_daily_summaries');
    }
};
