<?php

namespace App\Http\Controllers;

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItaMoitSubTopicController extends Controller
{
    public function index(Request $request)
    {
        $fiscalYears = ItaFiscalYear::orderByDesc('year')->get();

        $mainTopics = ItaMoitTopic::query()
            ->when($request->filled('fiscal_year_id'), function ($query) use ($request) {
                $query->where('fiscal_year_id', $request->integer('fiscal_year_id'));
            })
            ->orderByDesc('fiscal_year_id')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $subTopics = ItaMoitSubTopic::query()
            ->with(['fiscalYear', 'mainTopic'])
            ->withCount('documents')
            ->when($request->filled('fiscal_year_id'), function ($query) use ($request) {
                $query->where('fiscal_year_id', $request->integer('fiscal_year_id'));
            })
            ->when($request->filled('main_topic_id'), function ($query) use ($request) {
                $query->where('main_topic_id', $request->integer('main_topic_id'));
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = trim($request->keyword);

                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('code', 'like', "%{$keyword}%")
                        ->orWhere('title', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('fiscal_year_id')
            ->orderBy('main_topic_id')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->paginate(30)
            ->withQueryString();

        return view('ita.moit-sub-topics.index', compact(
            'subTopics',
            'fiscalYears',
            'mainTopics'
        ));
    }

    public function create()
    {
        $fiscalYears = ItaFiscalYear::orderByDesc('year')->get();

        $mainTopics = ItaMoitTopic::orderByDesc('fiscal_year_id')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        return view('ita.moit-sub-topics.create', compact('fiscalYears', 'mainTopics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year_id' => ['required', 'exists:ita_fiscal_years,id'],
            'main_topic_id' => [
                'required',
                Rule::exists('ita_moit_topics', 'id')
                    ->where('fiscal_year_id', $request->fiscal_year_id),
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ita_moit_sub_topics', 'code')
                    ->where('main_topic_id', $request->main_topic_id),
            ],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        ItaMoitSubTopic::create([
            'fiscal_year_id' => $validated['fiscal_year_id'],
            'main_topic_id' => $validated['main_topic_id'],
            'code' => $validated['code'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('ita.moit-sub-topics.index', [
                'fiscal_year_id' => $validated['fiscal_year_id'],
                'main_topic_id' => $validated['main_topic_id'],
            ])
            ->with('success', 'เพิ่มหัวข้อย่อย MOIT เรียบร้อยแล้ว');
    }

    public function edit(ItaMoitSubTopic $moitSubTopic)
    {
        $fiscalYears = ItaFiscalYear::orderByDesc('year')->get();

        $mainTopics = ItaMoitTopic::where('fiscal_year_id', $moitSubTopic->fiscal_year_id)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        return view('ita.moit-sub-topics.edit', compact(
            'moitSubTopic',
            'fiscalYears',
            'mainTopics'
        ));
    }

    public function update(Request $request, ItaMoitSubTopic $moitSubTopic)
    {
        $validated = $request->validate([
            'fiscal_year_id' => ['required', 'exists:ita_fiscal_years,id'],
            'main_topic_id' => [
                'required',
                Rule::exists('ita_moit_topics', 'id')
                    ->where('fiscal_year_id', $request->fiscal_year_id),
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ita_moit_sub_topics', 'code')
                    ->where('main_topic_id', $request->main_topic_id)
                    ->ignore($moitSubTopic->id),
            ],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $moitSubTopic->update([
            'fiscal_year_id' => $validated['fiscal_year_id'],
            'main_topic_id' => $validated['main_topic_id'],
            'code' => $validated['code'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('ita.moit-sub-topics.index', [
                'fiscal_year_id' => $validated['fiscal_year_id'],
                'main_topic_id' => $validated['main_topic_id'],
            ])
            ->with('success', 'แก้ไขหัวข้อย่อย MOIT เรียบร้อยแล้ว');
    }

    public function destroy(ItaMoitSubTopic $moitSubTopic)
    {
        if ($moitSubTopic->documents()->exists()) {
            return redirect()
                ->route('ita.moit-sub-topics.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะมีไฟล์เอกสารผูกอยู่');
        }

        $moitSubTopic->delete();

        return redirect()
            ->route('ita.moit-sub-topics.index')
            ->with('success', 'ลบหัวข้อย่อย MOIT เรียบร้อยแล้ว');
    }
}
