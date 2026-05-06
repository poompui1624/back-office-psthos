<?php

namespace App\Http\Controllers;

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitTopic;

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

        return view('ita.public.index', compact(
            'fiscalYears',
            'selectedYear',
            'topics',
            'indicators'
        ));
    }
}
