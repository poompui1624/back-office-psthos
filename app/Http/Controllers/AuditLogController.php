<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('audit.view'), 403);

        $search = $request->string('search')->toString();
        $action = $request->string('action')->toString();

        $logs = AuditLog::query()
            ->with('user')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('module', 'like', "%{$search}%")
                        ->orWhere('auditable_type', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($action, function ($query) use ($action) {
                $query->where('action', $action);
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('audit-logs.index', compact('logs', 'search', 'action'));
    }
}
