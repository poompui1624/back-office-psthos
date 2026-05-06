<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('employee_code', 50)->unique();

            $table->string('citizen_id', 20)->nullable()->unique();

            $table->string('prefix', 50)->nullable();
            $table->string('first_name');
            $table->string('last_name');

            $table->string('gender', 20)->nullable();
            $table->date('birth_date')->nullable();

            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('position_id')
                ->nullable()
                ->constrained('positions')
                ->nullOnDelete();

            $table->string('employment_type', 100)->nullable();
            $table->date('start_work_date')->nullable();

            $table->string('status', 50)->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->index('department_id');
            $table->index('position_id');
            $table->index('status');
            $table->index(['first_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
