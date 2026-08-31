<x-layouts.app title="รายละเอียดสลิปเงินเดือน">
    @php
        $incomeItems = $payslip->items->where('type', 'income')->sortBy('sort_order');
        $deductionItems = $payslip->items->where('type', 'deduction')->sortBy('sort_order');

        $who = trim($payslip->employee?->employee_code . ' - ' . $payslip->employee?->full_name, ' -');
    @endphp

    <x-page-header title="รายละเอียดสลิปเงินเดือน" :subtitle="$who">
        <x-btn :href="route('payslips.print', $payslip)" target="_blank" icon="document">พิมพ์สลิป</x-btn>

        <x-btn :href="route('payroll-periods.show', $payslip->payrollPeriod)" variant="secondary">ย้อนกลับ</x-btn>
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="รอบเงินเดือน" :value="$payslip->payrollPeriod?->name ?? '-'" icon="calendar" tone="violet" />
        <x-stat-card label="รายได้รวม" :value="number_format($payslip->gross_income, 2)" icon="money" tone="emerald" />
        <x-stat-card label="รายการหักรวม" :value="number_format($payslip->total_deduction, 2)" icon="trend-down" tone="rose" />
        <x-stat-card label="จ่ายสุทธิ" :value="number_format($payslip->net_pay, 2)" icon="money" />
    </div>

    <section class="card card-pad mb-6">
        <h2 class="section-title mb-4">ข้อมูลบุคลากร</h2>

        <dl class="grid gap-4 sm:grid-cols-2">
            @foreach ([
                'รหัสบุคลากร' => $payslip->employee?->employee_code,
                'ชื่อ - สกุล' => $payslip->employee?->full_name,
                'หน่วยงาน' => $payslip->employee?->department?->name,
                'ตำแหน่ง' => $payslip->employee?->position?->name,
            ] as $label => $value)
                <div>
                    <dt class="text-sm text-slate-500">{{ $label }}</dt>
                    <dd class="font-medium text-slate-900">{{ $value ?? '-' }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-stat-card label="มาสาย" :value="$payslip->late_minutes . ' นาที'" icon="clock"
                     :tone="$payslip->late_minutes > 0 ? 'amber' : 'brand'" />
        <x-stat-card label="กลับก่อน" :value="$payslip->early_leave_minutes . ' นาที'" icon="clock"
                     :tone="$payslip->early_leave_minutes > 0 ? 'amber' : 'brand'" />
        <x-stat-card label="ขาดงาน / ไม่พบสแกน" :value="$payslip->absent_days . ' วัน'" icon="clock"
                     :tone="$payslip->absent_days > 0 ? 'rose' : 'brand'" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{--
            Colour classes are written out in full rather than built from a tone
            name: Tailwind scans for literal strings, so an interpolated
            "text-{$tone}-700" is never generated.
        --}}
        @foreach ([
            ['heading' => 'รายได้', 'items' => $incomeItems, 'total' => $payslip->gross_income, 'totalLabel' => 'รวมรายได้', 'headingClass' => 'text-emerald-700', 'totalClass' => 'bg-emerald-50 text-emerald-700', 'empty' => 'ไม่มีรายการรายได้'],
            ['heading' => 'รายการหัก', 'items' => $deductionItems, 'total' => $payslip->total_deduction, 'totalLabel' => 'รวมรายการหัก', 'headingClass' => 'text-rose-700', 'totalClass' => 'bg-rose-50 text-rose-700', 'empty' => 'ไม่มีรายการหัก'],
        ] as $group)
            <section class="card card-pad">
                <h2 class="section-title mb-4 {{ $group['headingClass'] }}">{{ $group['heading'] }}</h2>

                <div class="overflow-hidden rounded-xl border border-slate-200">
                    @forelse ($group['items'] as $item)
                        <div class="flex items-start justify-between gap-3 border-b border-slate-100 px-4 py-2.5 text-sm">
                            <span class="text-slate-700">
                                {{ $item->name }}

                                @if ($item->quantity > 1)
                                    <span class="block text-xs text-slate-500">
                                        {{ $item->quantity }} x {{ number_format($item->unit_amount, 2) }}
                                    </span>
                                @endif
                            </span>

                            <span class="tabular-nums text-slate-900">{{ number_format($item->amount, 2) }}</span>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-slate-500">{{ $group['empty'] }}</div>
                    @endforelse

                    <div class="flex items-center justify-between gap-3 px-4 py-3 font-bold {{ $group['totalClass'] }}">
                        <span>{{ $group['totalLabel'] }}</span>
                        <span class="tabular-nums">{{ number_format($group['total'], 2) }}</span>
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    <div class="mt-6 rounded-2xl bg-brand-500 px-6 py-6 text-right text-white">
        <div class="text-sm text-brand-50">ยอดสุทธิที่ต้องจ่าย</div>
        <div class="mt-1 text-3xl font-bold tabular-nums">{{ number_format($payslip->net_pay, 2) }} บาท</div>
    </div>
</x-layouts.app>
