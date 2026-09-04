<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a report that changes nothing be recognised as such.
     *
     * Agents report daily but software changes perhaps twice a month, so
     * storing a snapshot every time wrote the same 16 KB of JSON over and over.
     */
    public function up(): void
    {
        Schema::table('computer_snapshots', function (Blueprint $table) {
            $table->char('software_hash', 64)->nullable()->after('installed_software');

            $table->index(['computer_id', 'software_hash']);
        });
    }

    public function down(): void
    {
        Schema::table('computer_snapshots', function (Blueprint $table) {
            $table->dropIndex(['computer_id', 'software_hash']);
            $table->dropColumn('software_hash');
        });
    }
};
