<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make overtime an explicit property of a shift and a rate on the employee.
     *
     * Until now OT was inferred by matching a shift's code or name against
     * "OT" and "ล่วงเวลา", which cannot be counted on and produced a count
     * rather than an amount.
     */
    public function up(): void
    {
        Schema::table('shift_types', function (Blueprint $table) {
            $table->boolean('is_ot')->default(false)->after('crosses_midnight');

            // Applied to the employee's hourly rate. 1.5 means time and a half.
            $table->decimal('ot_multiplier', 4, 2)->default(1);

            // When set, the shift pays this flat amount and the multiplier is ignored.
            $table->decimal('ot_flat_rate', 10, 2)->nullable();

            $table->index('is_ot');
        });

        Schema::table('salary_profiles', function (Blueprint $table) {
            $table->decimal('ot_rate_per_hour', 10, 2)->default(0)->after('absent_deduction_per_day');
        });

        // Carry the old heuristic forward once, so shifts already named as
        // overtime keep counting as overtime after this migration.
        DB::table('shift_types')
            ->where('code', 'like', '%OT%')
            ->orWhere('name', 'like', '%OT%')
            ->orWhere('name', 'like', '%ล่วงเวลา%')
            ->update(['is_ot' => true]);
    }

    public function down(): void
    {
        Schema::table('shift_types', function (Blueprint $table) {
            $table->dropIndex(['is_ot']);
            $table->dropColumn(['is_ot', 'ot_multiplier', 'ot_flat_rate']);
        });

        Schema::table('salary_profiles', function (Blueprint $table) {
            $table->dropColumn('ot_rate_per_hour');
        });
    }
};
