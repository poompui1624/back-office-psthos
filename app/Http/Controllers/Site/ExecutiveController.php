<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\SiteExecutive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExecutiveController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('site.view'), 403);

        return view('site.admin.executives.index', [
            'executives' => SiteExecutive::orderBy('sort_order')->orderBy('id')->paginate(30),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.executives.create', ['executive' => null]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $validated = $this->validated($request);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('site/executives', 'public');
        }

        unset($validated['photo']);

        SiteExecutive::create($validated);

        return redirect()->route('site.executives.index')->with('success', 'เพิ่มผู้บริหารเรียบร้อยแล้ว');
    }

    public function edit(SiteExecutive $executive)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.executives.edit', ['executive' => $executive]);
    }

    public function update(Request $request, SiteExecutive $executive)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $validated = $this->validated($request);

        if ($request->hasFile('photo')) {
            $this->deletePhoto($executive);
            $validated['photo_path'] = $request->file('photo')->store('site/executives', 'public');
        }

        unset($validated['photo']);

        $executive->update($validated);

        return redirect()->route('site.executives.index')->with('success', 'แก้ไขผู้บริหารเรียบร้อยแล้ว');
    }

    public function destroy(SiteExecutive $executive)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $this->deletePhoto($executive);
        $executive->delete();

        return redirect()->route('site.executives.index')->with('success', 'ลบผู้บริหารเรียบร้อยแล้ว');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // The model clears the flag on every other row when this one is saved,
        // so only the value posted here matters.
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    private function deletePhoto(SiteExecutive $executive): void
    {
        if ($executive->photo_path && Storage::disk('public')->exists($executive->photo_path)) {
            Storage::disk('public')->delete($executive->photo_path);
        }
    }
}
