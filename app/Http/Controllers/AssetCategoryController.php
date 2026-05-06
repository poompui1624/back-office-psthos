<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetCategoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('asset.view'), 403);

        $search = $request->string('search')->toString();

        $categories = AssetCategory::query()
            ->withCount('assets')
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        return view('asset-categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('asset.create'), 403);

        return view('asset-categories.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('asset.create'), 403);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:asset_categories,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        AssetCategory::create($validated);

        return redirect()
            ->route('asset-categories.index')
            ->with('success', 'เพิ่มหมวดหมู่พัสดุเรียบร้อยแล้ว');
    }

    public function edit(AssetCategory $assetCategory)
    {
        abort_unless(auth()->user()->can('asset.update'), 403);

        return view('asset-categories.edit', compact('assetCategory'));
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        abort_unless(auth()->user()->can('asset.update'), 403);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('asset_categories', 'code')->ignore($assetCategory->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $assetCategory->update($validated);

        return redirect()
            ->route('asset-categories.index')
            ->with('success', 'แก้ไขหมวดหมู่พัสดุเรียบร้อยแล้ว');
    }

    public function destroy(AssetCategory $assetCategory)
    {
        abort_unless(auth()->user()->can('asset.delete'), 403);

        if ($assetCategory->assets()->exists()) {
            return redirect()
                ->route('asset-categories.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะมีพัสดุอยู่ในหมวดหมู่นี้');
        }

        $assetCategory->delete();

        return redirect()
            ->route('asset-categories.index')
            ->with('success', 'ลบหมวดหมู่พัสดุเรียบร้อยแล้ว');
    }
}
