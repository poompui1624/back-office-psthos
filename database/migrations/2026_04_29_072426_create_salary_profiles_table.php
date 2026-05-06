<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('position_allowance', 12, 2)->default(0);
            $table->decimal('professional_allowance', 12, 2)->default(0);
            $table->decimal('other_allowance', 12, 2)->default(0);

            $table->decimal('social_security', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('provident_fund', 12, 2)->default(0);
            $table->decimal('other_deduction', 12, 2)->default(0);

            $table->decimal('late_deduction_per_minute', 8, 2)->default(0);
            $table->decimal('early_leave_deduction_per_minute', 8, 2)->default(0);
            $table->decimal('absent_deduction_per_day', 12, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique('employee_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_profiles');
    }
};
