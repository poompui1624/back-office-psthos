<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\SiteLink;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('site.view'), 403);

        return view('site.admin.links.index', [
            'links' => SiteLink::orderBy('sort_order')->orderBy('id')->paginate(30),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.links.create', ['link' => null]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        SiteLink::create($this->validated($request));

        return redirect()->route('site.links.index')->with('success', 'เพิ่มลิงก์เรียบร้อยแล้ว');
    }

    public function edit(SiteLink $link)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        return view('site.admin.links.edit', ['link' => $link]);
    }

    public function update(Request $request, SiteLink $link)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $link->update($this->validated($request));

        return redirect()->route('site.links.index')->with('success', 'แก้ไขลิงก์เรียบร้อยแล้ว');
    }

    public function destroy(SiteLink $link)
    {
        abort_unless(auth()->user()->can('site.manage'), 403);

        $link->delete();

        return redirect()->route('site.links.index')->with('success', 'ลบลิงก์เรียบร้อยแล้ว');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'opens_new_tab' => ['nullable', 'boolean'],
        ]);

        // Unchecked boxes post nothing, so both flags are read rather than
        // taken from the payload.
        $validated['is_active'] = $request->boolean('is_active');
        $validated['opens_new_tab'] = $request->boolean('opens_new_tab');

        return $validated;
    }
}
