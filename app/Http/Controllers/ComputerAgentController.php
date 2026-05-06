<?php

namespace App\Http\Controllers;

use App\Models\ComputerAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ComputerAgentController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('computer.agent_manage'), 403);

        $search = $request->string('search')->toString();

        $agents = ComputerAgent::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('last_ip_address', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('computer-agents.index', compact('agents', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('computer.agent_manage'), 403);

        return view('computer-agents.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('computer.agent_manage'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $plainToken = 'hospital-agent-' . Str::random(40);

        ComputerAgent::create([
            'name' => $validated['name'],
            'token_hash' => ComputerAgent::hashToken($plainToken),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('computer-agents.index')
            ->with('success', 'สร้าง Agent Token เรียบร้อยแล้ว')
            ->with('plain_token', $plainToken);
    }

    public function edit(ComputerAgent $computerAgent)
    {
        abort_unless(auth()->user()->can('computer.agent_manage'), 403);

        return view('computer-agents.edit', compact('computerAgent'));
    }

    public function update(Request $request, ComputerAgent $computerAgent)
    {
        abort_unless(auth()->user()->can('computer.agent_manage'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $computerAgent->update([
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('computer-agents.index')
            ->with('success', 'แก้ไข Agent เรียบร้อยแล้ว');
    }

    public function regenerateToken(ComputerAgent $computerAgent)
    {
        abort_unless(auth()->user()->can('computer.agent_manage'), 403);

        $plainToken = 'hospital-agent-' . Str::random(40);

        $computerAgent->update([
            'token_hash' => ComputerAgent::hashToken($plainToken),
            'is_active' => true,
        ]);

        return redirect()
            ->route('computer-agents.index')
            ->with('success', 'สร้าง Token ใหม่เรียบร้อยแล้ว')
            ->with('plain_token', $plainToken);
    }

    public function destroy(ComputerAgent $computerAgent)
    {
        abort_unless(auth()->user()->can('computer.agent_manage'), 403);

        $computerAgent->delete();

        return redirect()
            ->route('computer-agents.index')
            ->with('success', 'ลบ Agent เรียบร้อยแล้ว');
    }
}
