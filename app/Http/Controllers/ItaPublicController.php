<?php

namespace App\Http\Controllers;

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitTopic;
use Illuminate\Support\Collection;

class ItaPublicController extends Controller
{
    public function index(?int $year = null)
    {
        $fiscalYears = ItaFiscalYear::where('is_active', true)
            ->orderByDesc('year')
            ->get();

        $selectedYear = $year
            ? ItaFiscalYear::where('year', $year)->first()
            : $fiscalYears->first();

        abort_unless($selectedYear, 404);

        $topics = ItaMoitTopic::where('fiscal_year_id', $selectedYear->id)
            ->where('is_active', true)
            ->with([
                'documents' => function ($query) {
                    $query->where('is_public', true)
                        ->whereNull('sub_topic_id')
                        ->latest();
                },
                'subTopics' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('code')
                        ->with([
                            'documents' => function ($documentQuery) {
                                $documentQuery->where('is_public', true)
                                    ->latest();
                            },
                        ]);
                },
            ])
            ->orderBy('indicator_no')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $indicators = $topics->groupBy('indicator_no');

        return view('ita.public.index', [
            'fiscalYears' => $fiscalYears,
            'selectedYear' => $selectedYear,
            'topics' => $topics,
            'indicators' => $indicators,
            'progress' => $this->progressFor($topics),
        ]);
    }

    /**
     * How much of the assessment has been published, overall and per topic.
     *
     * Published against total is the question this page exists to answer, so it
     * is counted here rather than left for a reader to work out by scanning.
     * Heading items are excluded: they introduce the items beneath them and
     * never carry a file, so counting them would make a complete topic look
     * incomplete.
     *
     * @param  Collection<int, ItaMoitTopic>  $topics
     * @return array{total: int, published: int, percent: int, byTopic: array<int, array{total: int, published: int}>, byIndicator: array<int, array{total: int, published: int, percent: int}>}
     */
    private function progressFor($topics): array
    {
        $byTopic = [];
        $byIndicator = [];

        foreach ($topics as $topic) {
            $items = $topic->subTopics->where('is_heading', false);

            if ($items->isEmpty()) {
                // A topic with no items of its own stands as a single entry.
                $total = 1;
                $published = $topic->documents->isNotEmpty() ? 1 : 0;
            } else {
                $total = $items->count();
                $published = $items->filter(fn ($item) => $item->documents->isNotEmpty())->count();
            }

            $byTopic[$topic->id] = ['total' => $total, 'published' => $published];

            $byIndicator[$topic->indicator_no]['total'] = ($byIndicator[$topic->indicator_no]['total'] ?? 0) + $total;
            $byIndicator[$topic->indicator_no]['published'] = ($byIndicator[$topic->indicator_no]['published'] ?? 0) + $published;
        }

        foreach ($byIndicator as $no => $counts) {
            $byIndicator[$no]['percent'] = $counts['total'] > 0
                ? (int) round($counts['published'] / $counts['total'] * 100)
                : 0;
        }

        $total = array_sum(array_column($byTopic, 'total'));
        $published = array_sum(array_column($byTopic, 'published'));

        return [
            'total' => $total,
            'published' => $published,
            'percent' => $total > 0 ? (int) round($published / $total * 100) : 0,
            'byTopic' => $byTopic,
            'byIndicator' => $byIndicator,
        ];
    }
}
