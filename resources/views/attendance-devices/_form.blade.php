<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block font-medium">
            รหัสเครื่อง <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="code"
               value="{{ old('code', $attendanceDevice->code ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น DEVICE01, FINGER_OPD">

        @error('code')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">
            ชื่อเครื่อง <span class="text-red-600">*</span>
        </label>

        <input type="text"
               name="name"
               value="{{ old('name', $attendanceDevice->name ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น เครื่องสแกนหน้า OPD">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">สถานที่ติดตั้ง</label>

        <input type="text"
               name="location"
               value="{{ old('location', $attendanceDevice->location ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น หน้าอาคาร OPD, ห้องบัตร">

        @error('location')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">IP Address</label>

        <input type="text"
               name="ip_address"
               value="{{ old('ip_address', $attendanceDevice->ip_address ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น 192.168.1.50">

        @error('ip_address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">ยี่ห้อ</label>

        <input type="text"
               name="brand"
               value="{{ old('brand', $attendanceDevice->brand ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น ZKTeco">

        @error('brand')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block font-medium">รุ่น</label>

        <input type="text"
               name="model"
               value="{{ old('model', $attendanceDevice->model ?? '') }}"
               class="w-full rounded border-gray-300"
               placeholder="เช่น K40, MB460">

        @error('model')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block font-medium">หมายเหตุ</label>

        <textarea name="remark"
                  rows="3"
                  class="w-full rounded border-gray-300">{{ old('remark', $attendanceDevice->remark ?? '') }}</textarea>

        @error('remark')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   @checked(old('is_active', $attendanceDevice->is_active ?? true))>

            <span>เปิดใช้งาน</span>
        </label>
    </div>
</div>
