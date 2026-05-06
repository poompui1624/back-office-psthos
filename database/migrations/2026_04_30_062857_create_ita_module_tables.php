<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ปีงบประมาณ ITA
        |--------------------------------------------------------------------------
        */
        Schema::create('ita_fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique(); // เช่น 2569
            $table->string('name')->nullable(); // เช่น ปีงบประมาณ 2569
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | หัวข้อหลัก MOIT
        |--------------------------------------------------------------------------
        | เช่น
        | ตัวชี้วัดที่ 1 การเปิดเผยข้อมูล
        | MOIT 1 หน่วยงานมีการวางระบบ...
        */
        Schema::create('ita_moit_topics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fiscal_year_id')
                ->constrained('ita_fiscal_years')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('indicator_no')->default(1); // ตัวชี้วัดที่ 1-9
            $table->string('indicator_title')->nullable(); // เช่น การเปิดเผยข้อมูล

            $table->string('code', 50); // เช่น MOIT 1, MOIT 2, MOIT 3
            $table->text('title'); // ชื่อหัวข้อหลัก
            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['fiscal_year_id', 'code']);
            $table->index(['fiscal_year_id', 'indicator_no']);
        });

        /*
        |--------------------------------------------------------------------------
        | หัวข้อย่อยของ MOIT
        |--------------------------------------------------------------------------
        | เช่น
        | 3.1 คำสั่งแต่งตั้งคณะทำงาน...
        | 3.2 แผนปฏิบัติการ...
        */
        Schema::create('ita_moit_sub_topics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fiscal_year_id')
                ->constrained('ita_fiscal_years')
                ->cascadeOnDelete();

            $table->foreignId('main_topic_id')
                ->constrained('ita_moit_topics')
                ->cascadeOnDelete();

            $table->string('code', 50); // เช่น 3.1, 3.2, 3.4
            $table->text('title');
            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['main_topic_id', 'code']);
            $table->index(['fiscal_year_id', 'main_topic_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | ไฟล์เอกสาร ITA
        |--------------------------------------------------------------------------
        */
        Schema::create('ita_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fiscal_year_id')
                ->constrained('ita_fiscal_years')
                ->cascadeOnDelete();

            $table->foreignId('main_topic_id')
                ->constrained('ita_moit_topics')
                ->cascadeOnDelete();

            $table->foreignId('sub_topic_id')
                ->nullable()
                ->constrained('ita_moit_sub_topics')
                ->nullOnDelete();

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->string('file_original_name');
            $table->string('file_path');
            $table->string('file_mime')->nullable();
            $table->string('file_extension', 20)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('is_public')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['fiscal_year_id', 'main_topic_id', 'sub_topic_id']);
            $table->index(['is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ita_documents');
        Schema::dropIfExists('ita_moit_sub_topics');
        Schema::dropIfExists('ita_moit_topics');
        Schema::dropIfExists('ita_fiscal_years');
    }
};
