<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            $table->foreignId('from_department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('to_department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('from_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('to_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('moved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('moved_at');
            $table->string('reason')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();

            $table->index('asset_id');
            $table->index('from_department_id');
            $table->index('to_department_id');
            $table->index('from_employee_id');
            $table->index('to_employee_id');
            $table->index('moved_by');
            $table->index('moved_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};
