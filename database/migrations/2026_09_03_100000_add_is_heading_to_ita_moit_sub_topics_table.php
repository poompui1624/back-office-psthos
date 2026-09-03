<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mark the items that group other items rather than expecting a file.
     *
     * The public page strikes through any item with no document attached. That
     * is right for a document slot and wrong for an item like MOIT4's "ข้อ 3."
     * which exists to introduce 3.1 to 3.3 — it will never carry a file of its
     * own, so it was being shown as missing forever.
     */
    public function up(): void
    {
        Schema::table('ita_moit_sub_topics', function (Blueprint $table) {
            $table->boolean('is_heading')->default(false)->after('title');

            $table->index(['main_topic_id', 'is_heading']);
        });

        // An item that other items sit beneath is a heading. Applying it here
        // saves marking 43 of the 234 items by hand, and an editor can still
        // change any of them afterwards.
        //
        // Worked out in PHP rather than with a correlated UPDATE: the SQL for
        // that is MySQL-specific and the test suite runs on SQLite. At a few
        // hundred rows the difference does not matter.
        $items = DB::table('ita_moit_sub_topics')
            ->select('id', 'main_topic_id', 'code')
            ->get();

        $codesByTopic = [];

        foreach ($items as $item) {
            $codesByTopic[$item->main_topic_id][] = $item->code;
        }

        $headingIds = [];

        foreach ($items as $item) {
            foreach ($codesByTopic[$item->main_topic_id] as $code) {
                if (str_starts_with($code, $item->code.'.')) {
                    $headingIds[] = $item->id;
                    break;
                }
            }
        }

        if ($headingIds !== []) {
            DB::table('ita_moit_sub_topics')
                ->whereIn('id', $headingIds)
                ->update(['is_heading' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('ita_moit_sub_topics', function (Blueprint $table) {
            $table->dropIndex(['main_topic_id', 'is_heading']);
            $table->dropColumn('is_heading');
        });
    }
};
