<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('attendance_device_id')
                ->nullable()
                ->constrained('attendance_devices')
                ->nullOnDelete();

            $table->string('employee_code', 100)->nullable();
            $table->string('device_code', 100)->nullable();

            $table->dateTime('scan_time');
            $table->date('scan_date');

            $table->string('scan_type', 50)->nullable();
            $table->string('verify_type', 100)->nullable();

            $table->string('source', 50)->default('csv');
            $table->json('raw_data')->nullable();

            $table->timestamps();

            $table->unique(['employee_code', 'scan_time', 'device_code'], 'attendance_unique_scan');

            $table->index('employee_id');
            $table->index('attendance_device_id');
            $table->index('employee_code');
            $table->index('device_code');
            $table->index('scan_time');
            $table->index('scan_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
