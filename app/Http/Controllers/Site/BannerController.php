<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\SiteBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('site.view'), 403);

        return view('site.admin.banners.index', [
            'banners' => SiteBanner::orderBy('sort_order')->orderBy('id')->paginate(20),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.banners.create', ['banner' => null]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $validated = $this->validateBanner($request, imageRequired: true);

        $validated['image_path'] = $request->file('image')->store('site/banners', 'public');
        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['image']);

        SiteBanner::create($validated);

        return redirect()
            ->route('site.banners.index')
            ->with('success', 'เพิ่มแบนเนอร์เรียบร้อยแล้ว');
    }

    public function edit(SiteBanner $banner)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.banners.edit', ['banner' => $banner]);
    }

    public function update(Request $request, SiteBanner $banner)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $validated = $this->validateBanner($request, imageRequired: false);

        if ($request->hasFile('image')) {
            $this->deleteImage($banner);
            $validated['image_path'] = $request->file('image')->store('site/banners', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['image']);

        $banner->update($validated);

        return redirect()
            ->route('site.banners.index')
            ->with('success', 'แก้ไขแบนเนอร์เรียบร้อยแล้ว');
    }

    public function destroy(SiteBanner $banner)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $this->deleteImage($banner);
        $banner->delete();

        return redirect()
            ->route('site.banners.index')
            ->with('success', 'ลบแบนเนอร์เรียบร้อยแล้ว');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateBanner(Request $request, bool $imageRequired): array
    {
        return $request->validate([
            // Capped at 2MB and served at the size uploaded — nothing resizes
            // it, so an oversized banner is a slow homepage for every visitor.
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);
    }

    private function deleteImage(SiteBanner $banner): void
    {
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }
    }
}
