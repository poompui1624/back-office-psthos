<x-layouts.app title="สร้างรอบเงินเดือน">
    <x-page-header title="สร้างรอบเงินเดือน" subtitle="สร้างรอบเงินเดือนประจำเดือน แล้วนำไป Generate สลิป" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('payroll-periods.store') }}" class="space-y-6">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <x-form.field label="ปี" name="year" required>
                    <x-form.input type="number" name="year" :value="old('year', now()->year)" />
                </x-form.field>

                <x-form.field label="เดือน" name="month" required>
                    <x-form.select name="month">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected(old('month', now()->month) == $m)>
                                {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                            </option>
                        @endfor
                    </x-form.select>
                </x-form.field>

                <x-form.field label="หมายเหตุ" name="remark" class="md:col-span-2">
                    <x-form.textarea name="remark" rows="3" :value="old('remark')" />
                </x-form.field>
            </div>

            <x-alert type="info">
                ระบบจะสร้างช่วงวันที่เป็นวันแรกถึงวันสุดท้ายของเดือนอัตโนมัติ
            </x-alert>

            <x-form.actions :cancel="route('payroll-periods.index')" />
        </form>
    </div>
</x-layouts.app>
