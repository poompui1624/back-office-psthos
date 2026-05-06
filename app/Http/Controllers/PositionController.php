<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('position.view'), 403);

        $search = $request->string('search')->toString();

        $positions = Position::query()
            ->withCount('employees')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('level', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('positions.index', compact('positions', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('position.create'), 403);

        return view('positions.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('position.create'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:positions,name'],
            'level' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Position::create($validated);

        return redirect()
            ->route('positions.index')
            ->with('success', 'เพิ่มตำแหน่งเรียบร้อยแล้ว');
    }

    public function edit(Position $position)
    {
        abort_unless(auth()->user()->can('position.update'), 403);

        return view('positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        abort_unless(auth()->user()->can('position.update'), 403);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('positions', 'name')->ignore($position->id),
            ],
            'level' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $position->update($validated);

        return redirect()
            ->route('positions.index')
            ->with('success', 'แก้ไขตำแหน่งเรียบร้อยแล้ว');
    }

    public function destroy(Position $position)
    {
        abort_unless(auth()->user()->can('position.delete'), 403);

        if ($position->employees()->exists()) {
            return redirect()
                ->route('positions.index')
                ->with('error', 'ไม่สามารถลบได้ เพราะมีบุคลากรใช้ตำแหน่งนี้อยู่');
        }

        $position->delete();

        return redirect()
            ->route('positions.index')
            ->with('success', 'ลบตำแหน่งเรียบร้อยแล้ว');
    }
}
