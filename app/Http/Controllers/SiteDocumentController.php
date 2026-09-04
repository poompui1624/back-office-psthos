<?php

namespace App\Http\Controllers;

use App\Models\SiteDocument;
use App\Support\QrCode;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;

/**
 * Public reading of the document register.
 */
class SiteDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->string('category')->toString();
        $search = $request->string('search')->toString();

        if (! array_key_exists($category, SiteDocument::categories())) {
            $category = '';
        }

        return view('site.documents', [
            'category' => $category,
            'search' => $search,
            'documents' => SiteDocument::live()
                ->when($category, fn ($query) => $query->where('category', $category))
                ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%"))
                ->paginate(20)
                ->withQueryString(),
            'counts' => SiteDocument::live()
                ->reorder()
                ->select('category', DB::raw('count(*) as total'))
                ->groupBy('category')
                ->pluck('total', 'category'),
        ]);
    }

    public function show(SiteDocument $siteDocument): View
    {
        abort_unless($this->isLive($siteDocument), 404);

        return view('site.document', [
            'document' => $siteDocument,
            // Scanned off a printed notice to reach this page on a phone.
            'qrCode' => QrCode::inline(route('site.document', $siteDocument), 220),
        ]);
    }

    /**
     * Serve the file for the browser to render rather than save.
     *
     * Restricted to PDF, the only format a browser shows on its own. Serving a
     * visitor-facing file inline puts it on this site's own origin, so the
     * response carries a sandbox policy — a PDF can hold scripts, and without
     * it one would run with the site's privileges — plus nosniff, so a file
     * whose contents disagree with its extension is not re-interpreted.
     */
    public function preview(SiteDocument $siteDocument)
    {
        abort_unless($this->isLive($siteDocument), 404);
        abort_unless($siteDocument->isViewableInBrowser(), 404);
        abort_unless(Storage::disk('public')->exists($siteDocument->file_path), 404);

        $key = $siteDocument->getKey();

        defer(fn () => SiteDocument::whereKey($key)->increment('view_count'));

        return Storage::disk('public')->response($siteDocument->file_path, $siteDocument->file_original_name, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_INLINE,
                $siteDocument->file_original_name,
                $this->asciiFallback($siteDocument)
            ),
            'Content-Security-Policy' => "sandbox; default-src 'none'",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(SiteDocument $siteDocument)
    {
        abort_unless($this->isLive($siteDocument), 404);
        abort_unless(Storage::disk('public')->exists($siteDocument->file_path), 404);

        $key = $siteDocument->getKey();

        defer(fn () => SiteDocument::whereKey($key)->increment('download_count'));

        return Storage::disk('public')->download(
            $siteDocument->file_path,
            $siteDocument->file_original_name,
            ['Content-Disposition' => $this->contentDisposition($siteDocument)]
        );
    }

    private function isLive(SiteDocument $siteDocument): bool
    {
        return $siteDocument->is_published
            && $siteDocument->published_at
            && $siteDocument->published_at->isPast();
    }

    /**
     * Carries the real name in filename*, with an ASCII fallback for clients
     * that do not read it — transliterating Thai leaves nothing behind, so
     * without one the browser is offered a file called ".pdf".
     */
    private function contentDisposition(SiteDocument $siteDocument): string
    {
        return HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $siteDocument->file_original_name,
            $this->asciiFallback($siteDocument)
        );
    }

    /**
     * An ASCII name for clients that do not read filename*.
     *
     * Transliterating Thai leaves nothing behind, so without the final fallback
     * the browser would be offered a file called ".pdf".
     */
    private function asciiFallback(SiteDocument $siteDocument): string
    {
        $fallback = Str::ascii($siteDocument->file_original_name);
        $fallback = preg_replace('/[^A-Za-z0-9._-]+/', '-', $fallback) ?? '';
        $fallback = trim($fallback, '-');

        if ($fallback === '' || str_starts_with($fallback, '.')) {
            $extension = $siteDocument->file_extension ? '.'.$siteDocument->file_extension : '';
            $fallback = 'document-'.$siteDocument->getKey().$extension;
        }

        return $fallback;
    }
}
