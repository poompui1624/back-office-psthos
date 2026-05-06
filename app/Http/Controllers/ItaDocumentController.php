<?php

namespace App\Http\Controllers;

use App\Models\ItaDocument;
use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ItaDocumentController extends Controller
{
    public function index(Request $request)
    {
        $fiscalYears = ItaFiscalYear::orderByDesc('year')->get();

        $selectedYearId = $request->integer('fiscal_year_id');

        $documents = ItaDocument::query()
            ->with(['fiscalYear', 'mainTopic', 'subTopic', 'uploader'])
            ->when($selectedYearId, function ($query) use ($selectedYearId) {
                $query->where('fiscal_year_id', $selectedYearId);
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = trim($request->keyword);

                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('file_original_name', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('ita.documents.index', compact(
            'documents',
            'fiscalYears',
            'selectedYearId'
        ));
    }

    public function create()
    {
        $fiscalYears = ItaFiscalYear::where('is_active', true)
            ->orderByDesc('year')
            ->get();

        $mainTopics = ItaMoitTopic::where('is_active', true)
            ->with('fiscalYear')
            ->orderByDesc('fiscal_year_id')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        return view('ita.documents.create', compact(
            'fiscalYears',
            'mainTopics'
        ));
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
            'sub_topic_id' => [
                'nullable',
                Rule::exists('ita_moit_sub_topics', 'id')
                    ->where('fiscal_year_id', $request->fiscal_year_id)
                    ->where('main_topic_id', $request->main_topic_id),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_file' => [
                'required',
                'file',
                'max:20480',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png',
            ],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('document_file');

        $year = ItaFiscalYear::findOrFail($validated['fiscal_year_id']);

        $path = $file->store("ita/{$year->year}", 'public');

        ItaDocument::create([
            'fiscal_year_id' => $validated['fiscal_year_id'],
            'main_topic_id' => $validated['main_topic_id'],
            'sub_topic_id' => $validated['sub_topic_id'] ?? null,
            'title' => $validated['title'] ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'description' => $validated['description'] ?? null,
            'file_original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_mime' => $file->getMimeType(),
            'file_extension' => strtolower($file->getClientOriginalExtension()),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
            'is_public' => $request->boolean('is_public', true),
        ]);

        return redirect()
            ->route('ita.documents.index', ['fiscal_year_id' => $validated['fiscal_year_id']])
            ->with('success', 'อัปโหลดไฟล์ ITA เรียบร้อยแล้ว');
    }

    public function edit(ItaDocument $document)
    {
        $document->load(['fiscalYear', 'mainTopic', 'subTopic']);

        $fiscalYears = ItaFiscalYear::where('is_active', true)
            ->orderByDesc('year')
            ->get();

        $mainTopics = ItaMoitTopic::where('is_active', true)
            ->where('fiscal_year_id', $document->fiscal_year_id)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $subTopics = ItaMoitSubTopic::where('is_active', true)
            ->where('main_topic_id', $document->main_topic_id)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        return view('ita.documents.edit', compact(
            'document',
            'fiscalYears',
            'mainTopics',
            'subTopics'
        ));
    }

    public function update(Request $request, ItaDocument $document)
    {
        $validated = $request->validate([
            'fiscal_year_id' => ['required', 'exists:ita_fiscal_years,id'],
            'main_topic_id' => [
                'required',
                Rule::exists('ita_moit_topics', 'id')
                    ->where('fiscal_year_id', $request->fiscal_year_id),
            ],
            'sub_topic_id' => [
                'nullable',
                Rule::exists('ita_moit_sub_topics', 'id')
                    ->where('fiscal_year_id', $request->fiscal_year_id)
                    ->where('main_topic_id', $request->main_topic_id),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_file' => [
                'nullable',
                'file',
                'max:20480',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png',
            ],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $data = [
            'fiscal_year_id' => $validated['fiscal_year_id'],
            'main_topic_id' => $validated['main_topic_id'],
            'sub_topic_id' => $validated['sub_topic_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_public' => $request->boolean('is_public'),
        ];

        if ($request->hasFile('document_file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('document_file');
            $year = ItaFiscalYear::findOrFail($validated['fiscal_year_id']);

            $path = $file->store("ita/{$year->year}", 'public');

            $data['file_original_name'] = $file->getClientOriginalName();
            $data['file_path'] = $path;
            $data['file_mime'] = $file->getMimeType();
            $data['file_extension'] = strtolower($file->getClientOriginalExtension());
            $data['file_size'] = $file->getSize();
        }

        $document->update($data);

        return redirect()
            ->route('ita.documents.index', ['fiscal_year_id' => $validated['fiscal_year_id']])
            ->with('success', 'แก้ไขข้อมูลไฟล์ ITA เรียบร้อยแล้ว');
    }

    public function destroy(ItaDocument $document)
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('ita.documents.index')
            ->with('success', 'ลบไฟล์ ITA เรียบร้อยแล้ว');
    }

    public function subTopics(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => ['required', 'exists:ita_fiscal_years,id'],
            'main_topic_id' => ['required', 'exists:ita_moit_topics,id'],
        ]);

        $subTopics = ItaMoitSubTopic::where('is_active', true)
            ->where('fiscal_year_id', $request->fiscal_year_id)
            ->where('main_topic_id', $request->main_topic_id)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get(['id', 'code', 'title']);

        return response()->json($subTopics);
    }
}
