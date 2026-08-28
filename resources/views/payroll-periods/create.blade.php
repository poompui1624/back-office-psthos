<x-layouts.app title="สร้างรอบเงินเดือน">
    <x-page-header title="สร้างรอบเงินเดือน" subtitle="สร้างรอบเงินเดือนประจำเดือน แล้วนำไป Generate สลิป" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('payroll-periods.store') }}" class="space-y-6">
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

            <x-form.actions :cancel="route('payroll-periods.index')" />
        </form>
    </div>
</x-layouts.app>
