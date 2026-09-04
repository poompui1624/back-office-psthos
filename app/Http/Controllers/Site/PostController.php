<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\SitePost;
use App\Models\SitePostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('site.view'), 403);

        $category = $request->string('category')->toString();
        $search = $request->string('search')->toString();

        return view('site.admin.posts.index', [
            'category' => $category,
            'search' => $search,
            'posts' => SitePost::query()
                ->withCount('images')
                ->when($category, fn ($query) => $query->where('category', $category))
                ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%"))
                ->orderByDesc('is_pinned')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.posts.create', ['post' => null]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $validated = $this->validated($request);

        $post = DB::transaction(function () use ($request, $validated) {
            $post = SitePost::create($validated + [
                'slug' => SitePost::slugFor($validated['title']),
                'created_by' => auth()->id(),
                'cover_image_path' => $request->hasFile('cover_image')
                    ? $request->file('cover_image')->store('site/posts', 'public')
                    : null,
            ]);

            $this->storeGalleryImages($request, $post);

            return $post;
        });

        return redirect()
            ->route('site.posts.edit', $post)
            ->with('success', 'บันทึกเนื้อหาเรียบร้อยแล้ว');
    }

    public function edit(SitePost $post)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $post->load('images');

        return view('site.admin.posts.edit', ['post' => $post]);
    }

    public function update(Request $request, SitePost $post)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $validated = $this->validated($request);

        DB::transaction(function () use ($request, $post, $validated) {
            if ($request->hasFile('cover_image')) {
                $this->deleteFile($post->cover_image_path);
                $validated['cover_image_path'] = $request->file('cover_image')->store('site/posts', 'public');
            }

            // The slug is part of links already shared, so it only follows a
            // title change when the editor asks for it.
            if ($request->boolean('regenerate_slug')) {
                $validated['slug'] = SitePost::slugFor($validated['title'], $post->getKey());
            }

            $post->update($validated);

            $this->storeGalleryImages($request, $post);
        });

        return redirect()
            ->route('site.posts.edit', $post)
            ->with('success', 'บันทึกเนื้อหาเรียบร้อยแล้ว');
    }

    public function destroy(SitePost $post)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        // Soft-deleted, so the files stay: restoring a post that lost its
        // images would leave an article full of broken pictures.
        $post->delete();

        return redirect()
            ->route('site.posts.index')
            ->with('success', 'ลบเนื้อหาเรียบร้อยแล้ว');
    }

    public function destroyImage(SitePost $post, SitePostImage $image)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);
        abort_unless($image->site_post_id === $post->getKey(), 404);

        $this->deleteFile($image->image_path);
        $image->delete();

        return back()->with('success', 'ลบภาพเรียบร้อยแล้ว');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(SitePost::CATEGORIES))],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery_images' => ['nullable', 'array', 'max:20'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_pinned'] = $request->boolean('is_pinned');

        // Publishing without a time would leave the post invisible, since the
        // public scope requires one.
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        unset($validated['cover_image'], $validated['gallery_images']);

        return $validated;
    }

    private function storeGalleryImages(Request $request, SitePost $post): void
    {
        if (! $request->hasFile('gallery_images')) {
            return;
        }

        $nextOrder = (int) $post->images()->max('sort_order');

        foreach ($request->file('gallery_images') as $file) {
            $post->images()->create([
                'image_path' => $file->store('site/posts', 'public'),
                'sort_order' => ++$nextOrder,
            ]);
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
