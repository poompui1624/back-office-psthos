<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_devices', function (Blueprint $table) {
            $table->id();

            $table->string('code', 100)->unique();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('ip_address', 100)->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_devices');
    }
};
