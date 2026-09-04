<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Counts reading the file in the browser, kept apart from downloading it.
     *
     * Folding the two together would inflate the download figure with people
     * who only glanced at a notice and never saved it.
     */
    public function up(): void
    {
        Schema::table('site_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('view_count')->default(0)->after('download_count');
        });
    }

    public function down(): void
    {
        Schema::table('site_documents', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });
    }
};
