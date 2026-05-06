<x-layouts.app title="สร้างรอบเงินเดือน">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">สร้างรอบเงินเดือน</h1>
        <p class="text-sm text-gray-600">
            สร้างรอบเงินเดือนประจำเดือน แล้วนำไป Generate สลิป
        </p>
    </div>

    <div class="rounded bg-white p-6 shadow">
        <form method="POST" action="{{ route('payroll-periods.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block font-medium">
                        ปี <span class="text-red-600">*</span>
                    </label>

                    <input type="number"
                           name="year"
                           value="{{ old('year', now()->year) }}"
                           class="w-full rounded border-gray-300">

                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block font-medium">
                        เดือน <span class="text-red-600">*</span>
                    </label>

                    <select name="month" class="w-full rounded border-gray-300">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected(old('month', now()->month) == $m)>
                                {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                            </option>
                        @endfor
                    </select>

                    @error('month')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block font-medium">หมายเหตุ</label>

                    <textarea name="remark"
                              rows="3"
                              class="w-full rounded border-gray-300">{{ old('remark') }}</textarea>

                    @error('remark')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded bg-yellow-50 p-4 text-sm text-yellow-900">
                ระบบจะสร้างช่วงวันที่เป็นวันแรกถึงวันสุดท้ายของเดือนอัตโนมัติ
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    สร้างรอบเงินเดือน
                </button>

                <a href="{{ route('payroll-periods.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ย้อนกลับ
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
