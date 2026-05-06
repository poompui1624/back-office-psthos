<?php

namespace App\Http\Controllers;

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitTopic;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItaMoitTopicController extends Controller
{
    public function index(Request $request)
    {
        $fiscalYears = ItaFiscalYear::orderByDesc('year')->get();

        $topics = ItaMoitTopic::query()
            ->with('fiscalYear')
            ->withCount(['subTopics', 'documents'])
            ->when($request->filled('fiscal_year_id'), function ($query) use ($request) {
                $query->where('fiscal_year_id', $request->integer('fiscal_year_id'));
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = trim($request->keyword);

                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('code', 'like', "%{$keyword}%")
                        ->orWhere('title', 'like', "%{$keyword}%")
                        ->orWhere('indicator_title', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('fiscal_year_id')
            ->orderBy('indicator_no')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        return view('ita.moit-topics.index', compact('topics', 'fiscalYears'));
    }

    public function create()
    {
        $fiscalYears = ItaFiscalYear::orderByDesc('year')->get();

        return view('ita.moit-topics.create', compact('fiscalYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year_id' => ['required', 'exists:ita_fiscal_years,id'],
            'indicator_no' => ['required', 'integer', 'min:1', 'max:99'],
            'indicator_title' => ['nullable', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ita_moit_topics', 'code')
                    ->where('fiscal_year_id', $request->fiscal_year_id),
            ],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        ItaMoitTopic::create([
            'fiscal_year_id' => $validated['fiscal_year_id'],
            'indicator_no' => $validated['indicator_no'],
            'indicator_title' => $validated['indicator_title'] ?? null,
            'code' => $validated['code'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('ita.moit-topics.index', ['fiscal_year_id' => $validated['fiscal_year_id']])
            ->with('success', 'เพิ่มหัวข้อหลัก MOIT เรียบร้อยแล้ว');
    }

    public function edit(ItaMoitTopic $moitTopic)
    {
        $fiscalYears = ItaFiscalYear::orderByDesc('year')->get();

        return view('ita.moit-topics.edit', compact('moitTopic', 'fiscalYears'));
    }

    public function update(Request $request, ItaMoitTopic $moitTopic)
    {
        $validated = $request->validate([
            'fiscal_year_id' => ['required', 'exists:ita_fiscal_years,id'],
            'indicator_no' => ['required', 'integer', 'min:1', 'max:99'],
            'indicator_title' => ['nullable', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ita_moit_topics', 'code')
                    ->where('fiscal_year_id', $request->fiscal_year_id)
                    ->ignore($moitTopic->id),
            ],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $moitTopic->update([
            'fiscal_year_id' => $validated['fiscal_year_id'],
            'indicator_no' => $validated['indicator_no'],
            'indicator_title' => $validated['indicator_title'] ?? null,
            'code' => $validated['code'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('ita.moit-topics.index', ['fiscal_year_id' => $validated['fiscal_year_id']])
            ->with('success', 'แก้ไขหัวข้อหลัก MOIT เรียบร้อยแล้ว');
    }

    public function destroy(ItaMoitTopic $moitTopic)
    {
        if ($moitTopic->documents()->exists() || $moitTopic->subTopics()->exists()) {
            return redirect()
                ->route('ita.moit-topics.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะมีหัวข้อย่อยหรือไฟล์เอกสารผูกอยู่');
        }

        $moitTopic->delete();

        return redirect()
            ->route('ita.moit-topics.index')
            ->with('success', 'ลบหัวข้อหลัก MOIT เรียบร้อยแล้ว');
    }
}
