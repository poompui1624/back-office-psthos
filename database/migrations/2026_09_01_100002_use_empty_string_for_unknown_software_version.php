<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store an unknown version as '' rather than NULL.
     *
     * The unique key is (computer_id, normalized_name, version), and NULL is
     * never equal to NULL, so it did not constrain packages that report no
     * version. Every report inserted another row for those instead of updating
     * the existing one — on the machine this was found on, sixteen packages had
     * grown to four copies each after four reports.
     */
    public function up(): void
    {
        // Deduplicate first: the rows about to collapse onto one key would
        // otherwise violate the unique index the moment NULL becomes ''.
        $duplicates = DB::table('computer_software as later')
            ->join('computer_software as earlier', function ($join) {
                $join->on('later.computer_id', '=', 'earlier.computer_id')
                    ->on('later.normalized_name', '=', 'earlier.normalized_name')
                    ->whereRaw('coalesce(later.version, \'\') = coalesce(earlier.version, \'\')')
                    ->whereColumn('later.id', '>', 'earlier.id');
            })
            ->pluck('later.id')
            ->unique();

        if ($duplicates->isNotEmpty()) {
            DB::table('computer_software')->whereIn('id', $duplicates)->delete();
        }

        DB::table('computer_software')->whereNull('version')->update(['version' => '']);

        Schema::table('computer_software', function (Blueprint $table) {
            $table->string('version')->default('')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('computer_software', function (Blueprint $table) {
            $table->string('version')->nullable()->default(null)->change();
        });

        DB::table('computer_software')->where('version', '')->update(['version' => null]);
    }
};
