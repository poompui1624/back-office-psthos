<x-layouts.app title="รายละเอียดรอบเงินเดือน">
    @php
        $periodStatuses = ['draft' => 'ร่าง', 'generated' => 'สร้างสลิปแล้ว', 'closed' => 'ปิดรอบแล้ว'];
        $slipStatuses = ['draft' => 'ร่าง', 'approved' => 'อนุมัติแล้ว'];
        $slipTones = ['draft' => 'slate', 'approved' => 'success'];

        $range = $payrollPeriod->start_date?->format('Y-m-d') . ' ถึง ' . $payrollPeriod->end_date?->format('Y-m-d');
    @endphp

    <x-page-header :title="$payrollPeriod->name" :subtitle="$range">
        @if ($payrollPeriod->status !== 'closed')
            @can('payroll.generate')
                <form method="POST"
                      action="{{ route('payroll-periods.generate', $payrollPeriod) }}"
                      onsubmit="return confirm('ยืนยันการ Generate สลิปเงินเดือนรอบนี้?')">
                    @csrf

                    <x-btn type="submit" icon="refresh">Generate สลิป</x-btn>
                </form>
            @endcan

            @can('payroll.update')
                <form method="POST"
                      action="{{ route('payroll-periods.close', $payrollPeriod) }}"
                      onsubmit="return confirm('ยืนยันการปิดรอบเงินเดือนนี้? หลังปิดแล้วไม่ควร Generate ใหม่')">
                    @csrf

                    <x-btn type="submit" variant="success" icon="approvals">ปิดรอบ</x-btn>
                </form>
            @endcan
        @endif

        <x-btn :href="route('payroll-periods.index')" variant="secondary">ย้อนกลับ</x-btn>
    </x-page-header>

    <div class="mb-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="สถานะ" :value="$periodStatuses[$payrollPeriod->status] ?? $payrollPeriod->status"
                     icon="clipboard" tone="violet" />
        <x-stat-card label="จำนวนสลิป" :value="number_format($payslips->total())" icon="document" />
        <x-stat-card label="สร้างโดย" :value="$payrollPeriod->creator?->name ?? '-'" icon="user" tone="violet" />
        <x-stat-card label="Generate ล่าสุด" :value="$payrollPeriod->generated_at?->format('Y-m-d') ?? '-'"
                     icon="clock" tone="amber" />
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-stat-card label="รายได้รวม" :value="number_format($totals->gross_income, 2)" icon="money" tone="emerald" />
        <x-stat-card label="รายการหักรวม" :value="number_format($totals->total_deduction, 2)" icon="trend-down" tone="rose" />
        <x-stat-card label="จ่ายสุทธิรวม" :value="number_format($totals->net_pay, 2)" icon="money" />
    </div>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>บุคลากร</x-data-table.th>
            <x-data-table.th>หน่วยงาน</x-data-table.th>
            <x-data-table.th align="right">รายได้</x-data-table.th>
            <x-data-table.th align="right">รายการหัก</x-data-table.th>
            <x-data-table.th align="right">สุทธิ</x-data-table.th>
            <x-data-table.th align="center">มาสาย</x-data-table.th>
            <x-data-table.th align="center">กลับก่อน</x-data-table.th>
            <x-data-table.th align="center">ขาดงาน</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($payslips as $payslip)
            @php
                // items is eager loaded, so these filter in memory rather than
                // running two queries for every row on the page.
                $income = $payslip->items->where('type', 'income')->sortBy('sort_order');
                $deductions = $payslip->items->where('type', 'deduction')->sortBy('sort_order');
            @endphp

            <x-data-table.row>
                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $payslip->employee?->employee_code }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $payslip->employee?->full_name ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td>{{ $payslip->employee?->department?->name ?? '-' }}</x-data-table.td>

                <x-data-table.td align="right" class="tabular-nums text-emerald-700">
                    {{ number_format($payslip->gross_income, 2) }}
                </x-data-table.td>

                <x-data-table.td align="right" class="tabular-nums text-rose-700">
                    {{ number_format($payslip->total_deduction, 2) }}
                </x-data-table.td>

                <x-data-table.td align="right" class="tabular-nums font-bold text-slate-900">
                    {{ number_format($payslip->net_pay, 2) }}
                </x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">{{ $payslip->late_minutes }} นาที</x-data-table.td>
                <x-data-table.td align="center" class="tabular-nums">{{ $payslip->early_leave_minutes }} นาที</x-data-table.td>
                <x-data-table.td align="center" class="tabular-nums">{{ $payslip->absent_days }} วัน</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$slipTones[$payslip->status] ?? 'slate'" dot>
                        {{ $slipStatuses[$payslip->status] ?? $payslip->status }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-btn :href="route('payslips.show', $payslip)" variant="secondary" size="sm">ดูสลิป</x-btn>
                        <x-btn :href="route('payslips.print', $payslip)" target="_blank" size="sm">พิมพ์</x-btn>
                    </div>
                </x-data-table.td>
            </x-data-table.row>

            <tr class="bg-slate-50/70">
                <td colspan="10" class="px-4 py-3">
                    <details class="group">
                        <summary class="flex cursor-pointer items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900">
                            <x-icon name="chevron-right" class="h-3.5 w-3.5 transition group-open:rotate-90" />
                            ดูรายละเอียดรายการเงินเดือน
                        </summary>

                        <div class="mt-3 grid gap-4 md:grid-cols-2">
                            <div>
                                <h3 class="mb-2 font-bold text-emerald-700">รายได้</h3>

                                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                    @forelse ($income as $item)
                                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-3 py-2 text-sm last:border-0">
                                            <span class="text-slate-700">{{ $item->name }}</span>
                                            <span class="tabular-nums text-slate-900">{{ number_format($item->amount, 2) }}</span>
                                        </div>
                                    @empty
                                        <div class="px-3 py-2 text-sm text-slate-400">ไม่มีรายการ</div>
                                    @endforelse
                                </div>
                            </div>

                            <div>
                                <h3 class="mb-2 font-bold text-rose-700">รายการหัก</h3>

                                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                    @forelse ($deductions as $item)
                                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-3 py-2 text-sm last:border-0">
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
                                        <div class="px-3 py-2 text-sm text-slate-400">ไม่มีรายการ</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </details>
                </td>
            </tr>
        @empty
            <x-data-table.empty :colspan="10" icon="document" title="ยังไม่มีสลิปเงินเดือนในรอบนี้"
                                description="กด Generate สลิป เพื่อสร้างจากข้อมูลเงินเดือนและเวลาทำงาน" />
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $payslips->links() }}</div>
</x-layouts.app>
