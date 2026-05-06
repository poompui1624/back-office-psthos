<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('computers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->nullable()
                ->unique()
                ->constrained('assets')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('responsible_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('machine_uuid')->nullable()->unique();
            $table->string('hostname')->unique();

            $table->string('ip_address', 50)->nullable();
            $table->string('mac_address', 100)->nullable()->unique();

            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();

            $table->string('os_name')->nullable();
            $table->string('os_version')->nullable();

            $table->string('cpu_name')->nullable();
            $table->unsignedInteger('ram_gb')->nullable();
            $table->unsignedInteger('storage_gb')->nullable();

            $table->timestamp('last_seen_at')->nullable();

            $table->string('source', 50)->default('manual');
            $table->string('status', 50)->default('active');
            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('department_id');
            $table->index('responsible_employee_id');
            $table->index('hostname');
            $table->index('ip_address');
            $table->index('status');
            $table->index('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('computers');
    }
};