<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_licenses', function (Blueprint $table) {
            $table->timestamp('last_expire_notified_at')
                ->nullable()
                ->after('cancelled_at');

            $table->index('last_expire_notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('software_licenses', function (Blueprint $table) {
            $table->dropIndex(['last_expire_notified_at']);
            $table->dropColumn('last_expire_notified_at');
        });
    }
};
