<?php

namespace App\Http\Controllers;

use App\Models\SoftwareProduct;
use Illuminate\Http\Request;

class SoftwareProductController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('software.view'), 403);

        $search = $request->string('search')->toString();

        $products = SoftwareProduct::query()
            ->withCount('licenses')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('software-products.index', compact('products', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('software.create'), 403);

        return view('software-products.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('software.create'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        SoftwareProduct::create($validated);

        return redirect()
            ->route('software-products.index')
            ->with('success', 'เพิ่ม Software Product เรียบร้อยแล้ว');
    }

    public function edit(SoftwareProduct $softwareProduct)
    {
        abort_unless(auth()->user()->can('software.update'), 403);

        return view('software-products.edit', compact('softwareProduct'));
    }

    public function update(Request $request, SoftwareProduct $softwareProduct)
    {
        abort_unless(auth()->user()->can('software.update'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $softwareProduct->update($validated);

        return redirect()
            ->route('software-products.index')
            ->with('success', 'แก้ไข Software Product เรียบร้อยแล้ว');
    }

    public function destroy(SoftwareProduct $softwareProduct)
    {
        abort_unless(auth()->user()->can('software.delete'), 403);

        if ($softwareProduct->licenses()->exists()) {
            return redirect()
                ->route('software-products.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะมี License ผูกอยู่');
        }

        $softwareProduct->delete();

        return redirect()
            ->route('software-products.index')
            ->with('success', 'ลบ Software Product เรียบร้อยแล้ว');
    }
}
