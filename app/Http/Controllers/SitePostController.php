<?php

namespace App\Http\Controllers;

use App\Models\SitePost;
use App\Models\SitePostFile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;

/**
 * Public reading of news, activities, and knowledge articles.
 *
 * Every query goes through SitePost::live(), so a draft or a post scheduled for
 * later can never be reached from here.
 */
class SitePostController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->string('category')->toString();

        if (! array_key_exists($category, SitePost::CATEGORIES)) {
            $category = '';
        }

        return view('site.news', [
            'category' => $category,
            'posts' => SitePost::live()
                ->when($category, fn ($query) => $query->where('category', $category))
                ->paginate(12)
                ->withQueryString(),
            'counts' => SitePost::live()
                ->reorder()
                ->select('category', DB::raw('count(*) as total'))
                ->groupBy('category')
                ->pluck('total', 'category'),
        ]);
    }

    public function show(string $slug): View
    {
        $post = SitePost::live()->where('slug', $slug)->with(['images', 'files'])->first();

        abort_unless($post, 404);

        $this->recordView($post);

        return view('site.post', [
            'post' => $post,
            'related' => SitePost::live()
                ->where('category', $post->category)
                ->whereKeyNot($post->getKey())
                ->limit(3)
                ->get(),
        ]);
    }

    public function gallery(): View
    {
        return view('site.gallery', [
            'posts' => SitePost::live()
                ->where('category', 'activity')
                ->with('images')
                ->paginate(9),
        ]);
    }

    /**
     * Serve an attachment from a published post.
     *
     * The file lives on the public disk, but the link is served through here so
     * a document attached to a draft — or to a post scheduled for later — is
     * not reachable just because someone guessed the storage path.
     */
    public function download(SitePostFile $file)
    {
        $post = $file->post;

        abort_unless(
            $post
            && $post->is_published
            && $post->published_at
            && $post->published_at->isPast(),
            404
        );

        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        $key = $file->getKey();

        defer(fn () => SitePostFile::whereKey($key)->increment('download_count'));

        return Storage::disk('public')->download(
            $file->file_path,
            $file->file_original_name,
            ['Content-Disposition' => $this->contentDisposition($file)]
        );
    }

    /**
     * A Content-Disposition that survives a Thai filename.
     *
     * The header carries the real name in filename*, which every current
     * browser reads, plus a plain ASCII fallback for anything that does not.
     * Left to itself that fallback comes out as bare ".pdf", because
     * transliterating Thai leaves nothing behind.
     */
    private function contentDisposition(SitePostFile $file): string
    {
        $fallback = Str::ascii($file->file_original_name);
        $fallback = preg_replace('/[^A-Za-z0-9._-]+/', '-', $fallback) ?? '';
        $fallback = trim($fallback, '-');

        if ($fallback === '' || str_starts_with($fallback, '.')) {
            $extension = $file->file_extension ? '.'.$file->file_extension : '';
            $fallback = 'document-'.$file->getKey().$extension;
        }

        return HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $file->file_original_name,
            $fallback
        );
    }

    /**
     * Count the read without making the reader wait for it.
     *
     * Incrementing inline puts a write on the request path of the most-visited
     * pages on the site, and a counter is not worth that.
     */
    private function recordView(SitePost $post): void
    {
        $key = $post->getKey();

        defer(function () use ($key) {
            // Without this the counter would touch updated_at, making every
            // read look like an edit in the admin list.
            SitePost::withoutTimestamps(
                fn () => SitePost::whereKey($key)->increment('view_count')
            );
        });
    }
}
