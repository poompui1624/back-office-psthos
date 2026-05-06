<x-layouts.app title="โอนย้ายพัสดุ">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">โอนย้ายพัสดุ</h1>
        <p class="text-sm text-gray-600">บันทึกการย้ายหน่วยงานหรือเปลี่ยนผู้รับผิดชอบพัสดุ</p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('asset-movements.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1 block font-medium">พัสดุ <span class="text-red-600">*</span></label>
                <select name="asset_id" class="w-full rounded border-gray-300">
                    <option value="">-- เลือกพัสดุ --</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}"
                            @selected(old('asset_id', $selectedAsset?->id) == $asset->id)>
                            {{ $asset->asset_code }} - {{ $asset->name }}
                            | หน่วยงานเดิม: {{ $asset->department?->name ?? '-' }}
                            | ผู้รับผิดชอบเดิม: {{ $asset->responsibleEmployee?->full_name ?? '-' }}
                        </option>
                    @endforeach
                </select>

                @error('asset_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block font-medium">ย้ายไปหน่วยงาน</label>
                    <select name="to_department_id" class="w-full rounded border-gray-300">
                        <option value="">-- เลือกหน่วยงาน --</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                @selected(old('to_department_id') == $department->id)>
                                {{ $department->code }} - {{ $department->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('to_department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block font-medium">ผู้รับผิดชอบใหม่</label>
                    <select name="to_employee_id" class="w-full rounded border-gray-300">
                        <option value="">-- เลือกบุคลากร --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                @selected(old('to_employee_id') == $employee->id)>
                                {{ $employee->employee_code }} - {{ $employee->full_name }}
                            </option>
                        @endforeach
                    </select>

                    @error('to_employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block font-medium">วันที่โอนย้าย <span class="text-red-600">*</span></label>
                    <input type="date"
                           name="moved_at"
                           value="{{ old('moved_at', now()->format('Y-m-d')) }}"
                           class="w-full rounded border-gray-300">

                    @error('moved_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block font-medium">เหตุผล</label>
                    <input type="text"
                           name="reason"
                           value="{{ old('reason') }}"
                           class="w-full rounded border-gray-300"
                           placeholder="เช่น ย้ายหน่วยงาน, เปลี่ยนผู้รับผิดชอบ, ส่งมอบใหม่">

                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1 block font-medium">หมายเหตุ</label>
                <textarea name="remark"
                          rows="3"
                          class="w-full rounded border-gray-300">{{ old('remark') }}</textarea>

                @error('remark')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    บันทึกการโอนย้าย
                </button>

                <a href="{{ route('asset-movements.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
