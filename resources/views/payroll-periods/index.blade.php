<x-layouts.app title="รอบเงินเดือน">
    @php
        $periodStatuses = ['draft' => 'ร่าง', 'generated' => 'สร้างสลิปแล้ว', 'closed' => 'ปิดรอบแล้ว'];
        $periodTones = ['draft' => 'slate', 'generated' => 'info', 'closed' => 'success'];
    @endphp

    <x-page-header title="รอบเงินเดือน" subtitle="สร้างรอบเงินเดือนและ Generate สลิปเงินเดือน">
        <x-btn :href="route('salary-profiles.index')" variant="secondary" icon="cog">ตั้งค่าเงินเดือน</x-btn>

        @can('payroll.create')
            <x-btn :href="route('payroll-periods.create')" icon="money">สร้างรอบเงินเดือน</x-btn>
        @endcan
    </x-page-header>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>รอบเงินเดือน</x-data-table.th>
            <x-data-table.th align="center">ปี/เดือน</x-data-table.th>
            <x-data-table.th align="center">ช่วงวันที่</x-data-table.th>
            <x-data-table.th align="center">จำนวนสลิป</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th>หมายเหตุ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($periods as $period)
            <x-data-table.row>
                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $period->name }}</div>

                    @if ($period->generated_at)
                        <div class="mt-0.5 text-xs text-slate-500">
                            Generate: {{ $period->generated_at->format('Y-m-d H:i') }}
                        </div>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">
                    {{ str_pad($period->month, 2, '0', STR_PAD_LEFT) }}/{{ $period->year }}
                </x-data-table.td>

                <x-data-table.td align="center" class="whitespace-nowrap tabular-nums">
                    {{ $period->start_date?->format('Y-m-d') }} ถึง {{ $period->end_date?->format('Y-m-d') }}
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge tone="brand">{{ $period->payslips_count }}</x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$periodTones[$period->status] ?? 'slate'" dot>
                        {{ $periodStatuses[$period->status] ?? $period->status }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td>{{ $period->remark ?? '-' }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-btn :href="route('payroll-periods.show', $period)" variant="secondary" size="sm">
                        รายละเอียด
                    </x-btn>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="money" title="ยังไม่มีรอบเงินเดือน"
                                description="สร้างรอบเงินเดือนเพื่อเริ่ม Generate สลิป">
                @can('payroll.create')
                    <x-btn :href="route('payroll-periods.create')">สร้างรอบเงินเดือน</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $periods->links() }}</div>
</x-layouts.app>
