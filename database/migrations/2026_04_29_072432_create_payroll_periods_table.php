<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();

            $table->integer('year');
            $table->integer('month');

            $table->date('start_date');
            $table->date('end_date');

            $table->string('name');
            $table->string('status', 50)->default('draft');

            $table->timestamp('generated_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['year', 'month']);
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
