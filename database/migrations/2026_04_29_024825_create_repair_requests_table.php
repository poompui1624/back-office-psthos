<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();

            $table->string('ticket_no')->unique();

            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('requester_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('repairable_type')->nullable();
            $table->unsignedBigInteger('repairable_id')->nullable();

            $table->string('category', 100)->default('general');
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('location')->nullable();
            $table->string('priority', 50)->default('normal');

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status', 50)->default('new');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('solution')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('ticket_no');
            $table->index('requested_by');
            $table->index('requester_employee_id');
            $table->index('department_id');
            $table->index(['repairable_type', 'repairable_id']);
            $table->index('category');
            $table->index('priority');
            $table->index('assigned_to');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_requests');
    }
};
