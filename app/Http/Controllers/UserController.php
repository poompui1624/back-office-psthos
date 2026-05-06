<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('user.view'), 403);

        $search = $request->string('search')->toString();

        $users = User::query()
            ->with(['employee.department', 'roles'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($employeeQuery) use ($search) {
                            $employeeQuery->where('employee_code', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('user.create'), 403);

        $employees = Employee::query()
            ->where('status', 'active')
            ->whereDoesntHave('user')
            ->orderBy('employee_code')
            ->get();

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return view('users.create', compact('employees', 'roles'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('user.create'), 403);

        $validated = $request->validate([
            'employee_id' => [
                'nullable',
                'exists:employees,id',
                'unique:users,employee_id',
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'is_active' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        $user = User::create([
            'employee_id' => $validated['employee_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()
            ->route('users.index')
            ->with('success', 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function edit(User $user)
    {
        abort_unless(auth()->user()->can('user.update'), 403);

        $employees = Employee::query()
            ->where('status', 'active')
            ->where(function ($query) use ($user) {
                $query->whereDoesntHave('user')
                    ->orWhere('id', $user->employee_id);
            })
            ->orderBy('employee_code')
            ->get();

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        $userRoles = $user->roles->pluck('name')->toArray();

        return view('users.edit', compact('user', 'employees', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user()->can('user.update'), 403);

        $validated = $request->validate([
            'employee_id' => [
                'nullable',
                'exists:employees,id',
                Rule::unique('users', 'employee_id')->ignore($user->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'is_active' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        $data = [
            'employee_id' => $validated['employee_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()
            ->route('users.index')
            ->with('success', 'แก้ไขผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function destroy(User $user)
    {
        abort_unless(auth()->user()->can('user.delete'), 403);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'ไม่สามารถลบบัญชีที่กำลังใช้งานอยู่ได้');
        }

        if ($user->hasRole('super_admin') && User::role('super_admin')->count() <= 1) {
            return redirect()
                ->route('users.index')
                ->with('error', 'ไม่สามารถลบ super_admin คนสุดท้ายได้');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }
}
