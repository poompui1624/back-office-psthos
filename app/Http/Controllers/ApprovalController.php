<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('approval.view'), 403);

        $status = $request->string('status')->toString();
        $search = $request->string('search')->toString();

        $approvals = ApprovalRequest::query()
            ->with(['requester', 'approver', 'actions.user'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('requester', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('approvals.index', compact('approvals', 'status', 'search'));
    }

    public function approve(Request $request, ApprovalRequest $approval)
    {
        abort_unless(auth()->user()->can('approval.approve'), 403);

        if (! $approval->isPending()) {
            return back()->with('error', 'รายการนี้ไม่ได้อยู่ในสถานะรออนุมัติ');
        }

        $validated = $request->validate([
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        ApprovalService::approve(
            approval: $approval,
            user: auth()->user(),
            comment: $validated['comment'] ?? null
        );

        return back()->with('success', 'อนุมัติรายการเรียบร้อยแล้ว');
    }

    public function reject(Request $request, ApprovalRequest $approval)
    {
        abort_unless(auth()->user()->can('approval.reject'), 403);

        if (! $approval->isPending()) {
            return back()->with('error', 'รายการนี้ไม่ได้อยู่ในสถานะรออนุมัติ');
        }

        $validated = $request->validate([
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        ApprovalService::reject(
            approval: $approval,
            user: auth()->user(),
            comment: $validated['comment'] ?? null
        );

        return back()->with('success', 'ไม่อนุมัติรายการเรียบร้อยแล้ว');
    }
}
