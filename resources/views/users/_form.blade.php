<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">บุคลากร</label>
        <select name="employee_id" class="w-full rounded border-gray-300">
            <option value="">-- ไม่ผูกกับบุคลากร --</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    @selected(old('employee_id', $user->employee_id ?? '') == $employee->id)>
                    {{ $employee->employee_code }} - {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
        @error('employee_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ชื่อผู้ใช้ <span class="text-red-600">*</span></label>
        <input type="text"
               name="name"
               value="{{ old('name', $user->name ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">อีเมล <span class="text-red-600">*</span></label>
        <input type="email"
               name="email"
               value="{{ old('email', $user->email ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            รหัสผ่าน
            @isset($user)
                <span class="text-sm text-gray-500">(ไม่กรอก = ไม่เปลี่ยน)</span>
            @else
                <span class="text-red-600">*</span>
            @endisset
        </label>
        <input type="password"
               name="password"
               class="w-full rounded border-gray-300">
        @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            ยืนยันรหัสผ่าน
        </label>
        <input type="password"
               name="password_confirmation"
               class="w-full rounded border-gray-300">
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox"
               name="is_active"
               value="1"
               @checked(old('is_active', $user->is_active ?? true))>
        <label>เปิดใช้งาน</label>
    </div>
</div>

<div class="mt-6">
    <label class="mb-2 block font-medium">Role / สิทธิ์กลุ่มผู้ใช้</label>

    <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
        @foreach ($roles as $role)
            <label class="flex items-center gap-2 rounded border p-3">
                <input type="checkbox"
                       name="roles[]"
                       value="{{ $role->name }}"
                       @checked(in_array($role->name, old('roles', $userRoles ?? [])))>
                <span>{{ $role->name }}</span>
            </label>
        @endforeach
    </div>

    @error('roles')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
