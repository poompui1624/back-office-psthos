<?php

namespace Database\Seeders;

use App\Models\ItaDocument;
use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Loads the MOIT topic and item structure for a fiscal year.
 *
 * Safe to re-run: topics and items are matched on their code, so an existing
 * row is updated rather than duplicated, and any topic no longer in the source
 * is removed. Documents are never deleted — see {@see self::rehomeDocuments()}.
 */
class ItaMoitStructureSeeder extends Seeder
{
    private const YEAR = 2569;

    public function run(): void
    {
        $structure = require database_path('seeders/data/moit-2569.php');

        $fiscalYear = ItaFiscalYear::firstOrCreate(
            ['year' => self::YEAR],
            ['name' => 'ปีงบประมาณ '.self::YEAR, 'is_active' => true]
        );

        DB::transaction(function () use ($structure, $fiscalYear) {
            $keptTopicIds = [];
            $keptSubTopicIds = [];

            foreach ($structure as $order => $topic) {
                $topicModel = ItaMoitTopic::updateOrCreate(
                    ['fiscal_year_id' => $fiscalYear->id, 'code' => $topic['code']],
                    [
                        'indicator_no' => $topic['indicator_no'],
                        'indicator_title' => $topic['indicator_title'],
                        'title' => $topic['title'],
                        'sort_order' => $order + 1,
                        'is_active' => $topic['is_active'],
                    ]
                );

                $keptTopicIds[] = $topicModel->id;

                $codes = array_column($topic['items'], 0);

                foreach ($topic['items'] as [$code, $title]) {
                    $subTopic = ItaMoitSubTopic::updateOrCreate(
                        [
                            'fiscal_year_id' => $fiscalYear->id,
                            'main_topic_id' => $topicModel->id,
                            'code' => $code,
                        ],
                        [
                            'title' => $title,
                            'is_heading' => self::isHeading($code, $codes),
                            'sort_order' => self::sortOrderFor($code),
                            'is_active' => true,
                        ]
                    );

                    $keptSubTopicIds[] = $subTopic->id;
                }
            }

            $this->rehomeDocuments($fiscalYear, $keptTopicIds, $keptSubTopicIds);

            ItaMoitSubTopic::where('fiscal_year_id', $fiscalYear->id)
                ->whereNotIn('id', $keptSubTopicIds)
                ->delete();

            ItaMoitTopic::where('fiscal_year_id', $fiscalYear->id)
                ->whereNotIn('id', $keptTopicIds)
                ->delete();
        });

        $this->command?->info(sprintf(
            'MOIT %d: %d topics, %d items.',
            self::YEAR,
            ItaMoitTopic::where('fiscal_year_id', $fiscalYear->id)->count(),
            ItaMoitSubTopic::where('fiscal_year_id', $fiscalYear->id)->count()
        ));
    }

    /**
     * Whether an item introduces the items beneath it rather than expecting a file.
     *
     * MOIT4's "ข้อ 3." exists to head 3.1 to 3.3 and will never carry a document
     * of its own, so the public page must not show it as missing one.
     *
     * @param  array<int, string>  $allCodes
     */
    public static function isHeading(string $code, array $allCodes): bool
    {
        foreach ($allCodes as $other) {
            if (str_starts_with($other, $code.'.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * A sortable number for a dotted item code.
     *
     * The codes run to three levels ("6.2.1") and sorting them as text puts
     * "10" and "11" between "1.8" and "2". Each level gets its own place value
     * instead, so 18.4 stays after 9.
     */
    public static function sortOrderFor(string $code): int
    {
        $parts = array_map('intval', explode('.', $code));

        return ($parts[0] ?? 0) * 10000
            + ($parts[1] ?? 0) * 100
            + ($parts[2] ?? 0);
    }

    /**
     * Point documents at topics that survive this run.
     *
     * Uploaded files outlive the structure they were filed under, so a topic
     * that disappears must not take its documents with it. Anything left
     * pointing at a removed row is moved to the first item of the first topic,
     * where it stays visible and can be refiled by hand.
     *
     * @param  array<int, int>  $keptTopicIds
     * @param  array<int, int>  $keptSubTopicIds
     */
    private function rehomeDocuments(ItaFiscalYear $fiscalYear, array $keptTopicIds, array $keptSubTopicIds): void
    {
        // Only a topic that survives this run will do. Picking by sort_order
        // alone can land on a topic about to be deleted — an outgoing topic
        // and its replacement often share a position — which would move the
        // document onto a row that then takes it down with it.
        $fallbackTopic = ItaMoitTopic::whereIn('id', $keptTopicIds)
            ->orderBy('sort_order')
            ->first();

        if (! $fallbackTopic) {
            return;
        }

        $fallbackSubTopic = ItaMoitSubTopic::whereIn('id', $keptSubTopicIds)
            ->where('main_topic_id', $fallbackTopic->id)
            ->orderBy('sort_order')
            ->first();

        $orphaned = ItaDocument::where('fiscal_year_id', $fiscalYear->id)
            ->where(function ($query) use ($keptTopicIds, $keptSubTopicIds) {
                $query->whereNotIn('main_topic_id', $keptTopicIds)
                    ->orWhereNotIn('sub_topic_id', $keptSubTopicIds);
            })
            ->get();

        foreach ($orphaned as $document) {
            $document->update([
                'main_topic_id' => $fallbackTopic->id,
                'sub_topic_id' => $fallbackSubTopic?->id,
            ]);
        }

        if ($orphaned->isNotEmpty()) {
            $this->command?->warn(sprintf(
                '%d document(s) referenced a topic that no longer exists and were moved to %s %s.',
                $orphaned->count(),
                $fallbackTopic->code,
                $fallbackSubTopic?->code ?? ''
            ));
        }
    }
}
