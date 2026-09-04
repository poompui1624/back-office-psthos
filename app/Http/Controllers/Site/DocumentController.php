<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\SiteDocument;
use App\Support\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('site.view'), 403);

        $category = $request->string('category')->toString();
        $search = $request->string('search')->toString();

        return view('site.admin.documents.index', [
            'category' => $category,
            'search' => $search,
            'documents' => SiteDocument::query()
                ->when($category, fn ($query) => $query->where('category', $category))
                ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%"))
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.documents.create', ['document' => null]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $validated = $this->validated($request, fileRequired: true);

        $file = $request->file('document_file');

        $siteDocument = SiteDocument::create($validated + [
            'file_path' => $file->store('site/documents', 'public'),
            'file_original_name' => $file->getClientOriginalName(),
            'file_mime' => $file->getClientMimeType(),
            'file_extension' => mb_strtolower($file->getClientOriginalExtension()),
            'file_size' => $file->getSize(),
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('site.documents.edit', $siteDocument)
            ->with('success', 'อัปโหลดเอกสารเรียบร้อยแล้ว');
    }

    public function edit(SiteDocument $siteDocument)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.documents.edit', [
            'document' => $siteDocument,
            // Shown so staff can print it onto a notice board.
            'qrCode' => QrCode::inline(route('site.document', $siteDocument), 200),
        ]);
    }

    public function update(Request $request, SiteDocument $siteDocument)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $validated = $this->validated($request, fileRequired: false);

        if ($request->hasFile('document_file')) {
            $this->deleteFile($siteDocument->file_path);

            $file = $request->file('document_file');

            $validated += [
                'file_path' => $file->store('site/documents', 'public'),
                'file_original_name' => $file->getClientOriginalName(),
                'file_mime' => $file->getClientMimeType(),
                'file_extension' => mb_strtolower($file->getClientOriginalExtension()),
                'file_size' => $file->getSize(),
            ];
        }

        $siteDocument->update($validated);

        return redirect()
            ->route('site.documents.edit', $siteDocument)
            ->with('success', 'บันทึกเอกสารเรียบร้อยแล้ว');
    }

    public function destroy(SiteDocument $siteDocument)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        // Soft delete, keeping the file: a document restored without it would
        // be a row pointing at nothing.
        $siteDocument->delete();

        return redirect()
            ->route('site.documents.index')
            ->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, bool $fileRequired): array
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(SiteDocument::categories()))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_file' => [
                $fileRequired ? 'required' : 'nullable',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx',
                'max:20480',
            ],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        // Publishing without a time would leave it invisible, since the public
        // scope requires one.
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        unset($validated['document_file']);

        return $validated;
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
