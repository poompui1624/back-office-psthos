<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();

            $table->string('module', 100);
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('requestable_type')->nullable();
            $table->unsignedBigInteger('requestable_id')->nullable();

            $table->foreignId('requested_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('approver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status', 50)->default('pending');
            $table->json('data')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('remark')->nullable();

            $table->timestamps();

            $table->index('module');
            $table->index('status');
            $table->index('requested_by');
            $table->index('approver_id');
            $table->index(['requestable_type', 'requestable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
    }
};
