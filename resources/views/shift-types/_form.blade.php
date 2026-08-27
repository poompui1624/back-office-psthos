<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">
            รหัสเวร <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="code"
               value="{{ old('code', $shiftType->code ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น MORNING, NIGHT, OFFICE">

        @error('code')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            ชื่อเวร <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="name"
               value="{{ old('name', $shiftType->name ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น เวรเช้า, เวรบ่าย, เวรดึก">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            เวลาเริ่ม <span class="text-red-600">*</span>
        </label>

        <input type="time"
               name="start_time"
               value="{{ old('start_time', isset($shiftType) ? substr($shiftType->start_time, 0, 5) : '') }}"
               class="w-full rounded border-gray-300">

        @error('start_time')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            เวลาสิ้นสุด <span class="text-red-600">*</span>
        </label>

        <input type="time"
               name="end_time"
               value="{{ old('end_time', isset($shiftType) ? substr($shiftType->end_time, 0, 5) : '') }}"
               class="w-full rounded border-gray-300">

        @error('end_time')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">สี</label>

        <select name="color" class="w-full rounded border-gray-300">
            <option value="">-- ไม่ระบุ --</option>
            <option value="blue" @selected(old('color', $shiftType->color ?? '') === 'blue')>น้ำเงิน</option>
            <option value="green" @selected(old('color', $shiftType->color ?? '') === 'green')>เขียว</option>
            <option value="yellow" @selected(old('color', $shiftType->color ?? '') === 'yellow')>เหลือง</option>
            <option value="purple" @selected(old('color', $shiftType->color ?? '') === 'purple')>ม่วง</option>
            <option value="red" @selected(old('color', $shiftType->color ?? '') === 'red')>แดง</option>
            <option value="gray" @selected(old('color', $shiftType->color ?? '') === 'gray')>เทา</option>
        </select>
    </div>

    <div class="flex flex-col justify-end gap-2">
        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="crosses_midnight"
                   value="1"
                   @checked(old('crosses_midnight', $shiftType->crosses_midnight ?? false))>

            <span>เวรข้ามวัน</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_ot"
                   value="1"
                   @checked(old('is_ot', $shiftType->is_ot ?? false))>

            <span>นับเป็นเวรล่วงเวลา (OT)</span>
        </label>
        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   @checked(old('is_active', $shiftType->is_active ?? true))>

            <span>เปิดใช้งาน</span>
        </label>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">รายละเอียด</label>

        <textarea name="description"
                  rows="3"
                  class="w-full rounded border-gray-300">{{ old('description', $shiftType->description ?? '') }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="mb-1 block font-medium">ตัวคูณ OT</label>

        <input type="number"
               step="0.01"
               min="0"
               name="ot_multiplier"
               value="{{ old('ot_multiplier', $shiftType->ot_multiplier ?? 1) }}"
               class="w-full rounded border-gray-300">

        <p class="mt-1 text-sm text-gray-500">คูณกับอัตรารายชั่วโมงของบุคลากร เช่น 1.5 คือหนึ่งเท่าครึ่ง</p>

        @error('ot_multiplier')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ค่า OT เหมาจ่าย (บาท)</label>

        <input type="number"
               step="0.01"
               min="0"
               name="ot_flat_rate"
               value="{{ old('ot_flat_rate', $shiftType->ot_flat_rate ?? '') }}"
               class="w-full rounded border-gray-300">

        <p class="mt-1 text-sm text-gray-500">ถ้ากรอกไว้ จะจ่ายเท่านี้ต่อเวร และไม่ใช้ตัวคูณ</p>

        @error('ot_flat_rate')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
