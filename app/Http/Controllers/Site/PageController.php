<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * The three fixed pages the homepage places. They are seeded, so this offers
 * editing only — there is no create or delete, because a page the layout does
 * not know about would have nowhere to appear.
 */
class PageController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('site.view'), 403);

        // Ordered the way the homepage reads them rather than by id, which is
        // whatever order the seeder happened to insert.
        $order = array_keys(SitePage::KEYS);

        return view('site.admin.pages.index', [
            'pages' => SitePage::all()->sortBy(fn (SitePage $page) => array_search($page->key, $order, true))->values(),
        ]);
    }

    public function edit(SitePage $page)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.pages.edit', ['page' => $page]);
    }

    public function update(Request $request, SitePage $page)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($page->image_path && Storage::disk('public')->exists($page->image_path)) {
                Storage::disk('public')->delete($page->image_path);
            }

            $validated['image_path'] = $request->file('image')->store('site/pages', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['image']);

        $page->update($validated);

        return redirect()->route('site.pages.index')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }
}
