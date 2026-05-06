<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_licenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('software_product_id')
                ->constrained('software_products')
                ->cascadeOnDelete();

            $table->string('license_name')->nullable();
            $table->string('license_key')->nullable();
            $table->string('license_type')->nullable();

            $table->unsignedInteger('total_seats')->default(1);
            $table->unsignedInteger('used_seats')->default(0);

            $table->date('purchase_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->date('renewed_at')->nullable();
            $table->date('cancelled_at')->nullable();

            $table->decimal('price', 12, 2)->nullable();
            $table->string('vendor_contact')->nullable();

            $table->string('status', 50)->default('active');
            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('software_product_id');
            $table->index('expire_date');
            $table->index('status');
            $table->index('license_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_licenses');
    }
};
