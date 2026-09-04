<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_personnel_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->unique()
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->string('nationality', 100)->nullable();
            $table->string('ethnicity', 100)->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('blood_type', 10)->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('military_status', 100)->nullable();

            $table->string('birth_place')->nullable();
            $table->string('taxpayer_no', 50)->nullable();
            $table->string('social_security_no', 50)->nullable();
            $table->string('professional_license_no', 100)->nullable();
            $table->date('professional_license_expired_at')->nullable();

            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relation', 100)->nullable();
            $table->string('emergency_contact_phone', 50)->nullable();

            $table->json('registered_address')->nullable();
            $table->json('current_address')->nullable();
            $table->json('family_members')->nullable();
            $table->json('education_histories')->nullable();
            $table->json('training_histories')->nullable();
            $table->json('position_histories')->nullable();
            $table->json('salary_histories')->nullable();
            $table->json('service_histories')->nullable();
            $table->json('disciplinary_histories')->nullable();
            $table->json('decorations')->nullable();
            $table->json('name_change_histories')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('employee_id');
            $table->index('marital_status');
            $table->index('blood_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_personnel_profiles');
    }
};
