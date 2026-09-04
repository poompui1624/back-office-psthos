<?php

namespace App\Http\Controllers;

use App\Models\SitePost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $post = SitePost::live()->where('slug', $slug)->with('images')->first();

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
