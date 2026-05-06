<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('shift_type_id')
                ->constrained('shift_types')
                ->cascadeOnDelete();

            $table->date('work_date');

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->string('role_group', 100)->nullable();
            $table->string('status', 50)->default('assigned');

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employee_id', 'work_date', 'shift_type_id'], 'duty_schedule_unique');

            $table->index('employee_id');
            $table->index('department_id');
            $table->index('shift_type_id');
            $table->index('work_date');
            $table->index('start_at');
            $table->index('end_at');
            $table->index('role_group');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_schedules');
    }
};
