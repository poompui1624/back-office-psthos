<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('computer_snapshots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('computer_id')
                ->constrained('computers')
                ->cascadeOnDelete();

            $table->string('hostname')->nullable();
            $table->string('ip_address', 50)->nullable();

            $table->string('os_name')->nullable();
            $table->string('os_version')->nullable();

            $table->string('cpu_name')->nullable();
            $table->unsignedInteger('ram_gb')->nullable();
            $table->unsignedInteger('storage_gb')->nullable();

            $table->json('installed_software')->nullable();
            $table->json('raw_payload')->nullable();

            $table->timestamp('reported_at')->nullable();

            $table->timestamps();

            $table->index('computer_id');
            $table->index('reported_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('computer_snapshots');
    }
};
