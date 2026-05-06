<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            $table->string('request_no')->unique();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('leave_type_id')
                ->constrained('leave_types')
                ->cascadeOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->string('start_period', 20)->default('full');
            $table->string('end_period', 20)->default('full');

            $table->decimal('total_days', 5, 2)->default(1);

            $table->text('reason')->nullable();
            $table->string('contact_during_leave')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

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

            $table->string('status', 50)->default('pending');
            $table->text('approval_remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('request_no');
            $table->index('employee_id');
            $table->index('department_id');
            $table->index('leave_type_id');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('status');
            $table->index('created_by');
            $table->index('approved_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
