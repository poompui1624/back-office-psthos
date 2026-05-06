<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->string('asset_code', 100)->unique();
            $table->string('name');

            $table->foreignId('asset_category_id')
                ->nullable()
                ->constrained('asset_categories')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('responsible_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();

            $table->date('received_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->string('budget_source')->nullable();

            $table->string('location')->nullable();

            $table->string('status', 50)->default('active');
            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('asset_code');
            $table->index('asset_category_id');
            $table->index('department_id');
            $table->index('responsible_employee_id');
            $table->index('status');
            $table->index('serial_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
