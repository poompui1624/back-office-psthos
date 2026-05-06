<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">รหัสบุคลากร <span class="text-red-600">*</span></label>
        <input type="text"
               name="employee_code"
               value="{{ old('employee_code', $employee->employee_code ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น EMP001">
        @error('employee_code')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">เลขบัตรประชาชน</label>
        <input type="text"
               name="citizen_id"
               value="{{ old('citizen_id', $employee->citizen_id ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('citizen_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">คำนำหน้า</label>
        <input type="text"
               name="prefix"
               value="{{ old('prefix', $employee->prefix ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="นาย / นาง / นางสาว">
        @error('prefix')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">เพศ</label>
        <select name="gender" class="w-full rounded border-gray-300">
            <option value="">-- ไม่ระบุ --</option>
            <option value="male" @selected(old('gender', $employee->gender ?? '') === 'male')>ชาย</option>
            <option value="female" @selected(old('gender', $employee->gender ?? '') === 'female')>หญิง</option>
            <option value="other" @selected(old('gender', $employee->gender ?? '') === 'other')>อื่น ๆ</option>
        </select>
        @error('gender')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ชื่อ <span class="text-red-600">*</span></label>
        <input type="text"
               name="first_name"
               value="{{ old('first_name', $employee->first_name ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('first_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">นามสกุล <span class="text-red-600">*</span></label>
        <input type="text"
               name="last_name"
               value="{{ old('last_name', $employee->last_name ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('last_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">วันเกิด</label>
        <input type="date"
               name="birth_date"
               value="{{ old('birth_date', isset($employee) && $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}"
               class="w-full rounded border-gray-300">
        @error('birth_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">เบอร์โทร</label>
        <input type="text"
               name="phone"
               value="{{ old('phone', $employee->phone ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">อีเมล</label>
        <input type="email"
               name="email"
               value="{{ old('email', $employee->email ?? '') }}"
               class="w-full rounded border-gray-300">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ประเภทการจ้าง</label>
        <input type="text"
               name="employment_type"
               value="{{ old('employment_type', $employee->employment_type ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น ข้าราชการ / ลูกจ้าง / พนักงานราชการ">
        @error('employment_type')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">หน่วยงาน</label>
        <select name="department_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกหน่วยงาน --</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $employee->department_id ?? '') == $department->id)>
                    {{ $department->code }} - {{ $department->name }}
                </option>
            @endforeach
        </select>
        @error('department_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ตำแหน่ง</label>
        <select name="position_id" class="w-full rounded border-gray-300">
            <option value="">-- เลือกตำแหน่ง --</option>
            @foreach ($positions as $position)
                <option value="{{ $position->id }}"
                    @selected(old('position_id', $employee->position_id ?? '') == $position->id)>
                    {{ $position->name }}
                </option>
            @endforeach
        </select>
        @error('position_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">วันที่เริ่มงาน</label>
        <input type="date"
               name="start_work_date"
               value="{{ old('start_work_date', isset($employee) && $employee->start_work_date ? $employee->start_work_date->format('Y-m-d') : '') }}"
               class="w-full rounded border-gray-300">
        @error('start_work_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">สถานะ <span class="text-red-600">*</span></label>
        <select name="status" class="w-full rounded border-gray-300">
            <option value="active" @selected(old('status', $employee->status ?? 'active') === 'active')>
                ปฏิบัติงาน
            </option>
            <option value="inactive" @selected(old('status', $employee->status ?? '') === 'inactive')>
                ปิดใช้งาน
            </option>
            <option value="resigned" @selected(old('status', $employee->status ?? '') === 'resigned')>
                ลาออก
            </option>
            <option value="retired" @selected(old('status', $employee->status ?? '') === 'retired')>
                เกษียณ
            </option>
            <option value="transferred" @selected(old('status', $employee->status ?? '') === 'transferred')>
                ย้ายหน่วยงาน
            </option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
