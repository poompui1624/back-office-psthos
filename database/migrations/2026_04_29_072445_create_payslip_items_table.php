<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslip_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payslip_id')
                ->constrained('payslips')
                ->cascadeOnDelete();

            $table->string('type', 50);
            $table->string('code', 100);
            $table->string('name');

            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_amount', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);

            $table->integer('sort_order')->default(0);
            $table->text('remark')->nullable();

            $table->timestamps();

            $table->index('payslip_id');
            $table->index('type');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslip_items');
    }
};
