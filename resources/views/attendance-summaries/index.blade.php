<x-layouts.app title="สรุปเวลาทำงานรายวัน">
    @php
        // Declared here, not inside the filter slot: a component slot has its
        // own scope, and the table below needs the same labels.
        $statuses = [
            'normal' => 'ปกติ',
            'late' => 'มาสาย',
            'early_leave' => 'กลับก่อน',
            'late_and_early_leave' => 'สายและกลับก่อน',
            'incomplete' => 'ข้อมูลไม่ครบ',
        ];
    @endphp

    <x-page-header title="สรุปเวลาทำงานรายวัน" subtitle="สรุปเวลาเข้าออกงานจากข้อมูลเครื่องสแกนนิ้วมือ">
        <x-btn :href="route('attendance-summaries.dashboard')" variant="secondary" icon="chart">Dashboard</x-btn>
        <x-btn :href="route('attendance-logs.index')" variant="secondary" icon="clock">ข้อมูลเวลาสแกน</x-btn>

        <x-btn :href="route('attendance-summaries.print', request()->query())" target="_blank"
               variant="secondary" icon="document">พิมพ์รายงาน</x-btn>

        @can('attendance.import')
            <x-btn :href="route('attendance-summaries.generate-form')" icon="refresh">สร้างสรุปเวลา</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('attendance-summaries.index')">
        <x-form.field label="ค้นหา" class="min-w-56 flex-1">
            <x-form.input name="search" :value="$search" placeholder="รหัส / ชื่อบุคลากร / หน่วยงาน" />
        </x-form.field>

        <x-form.field label="ตั้งแต่วันที่">
            <x-form.input type="date" name="date_from" :value="$dateFrom" />
        </x-form.field>

        <x-form.field label="ถึงวันที่">
            <x-form.input type="date" name="date_to" :value="$dateTo" />
        </x-form.field>

        <x-form.field label="สถานะ">
            <x-form.select name="status" class="w-44">
                <option value="">ทุกสถานะ</option>

                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>วันที่</x-data-table.th>
            <x-data-table.th>บุคลากร</x-data-table.th>
            <x-data-table.th>หน่วยงาน</x-data-table.th>
            <x-data-table.th>เวร</x-data-table.th>
            <x-data-table.th align="center">เวลาเข้า</x-data-table.th>
            <x-data-table.th align="center">เวลาออก</x-data-table.th>
            <x-data-table.th align="center">เวลาทำงาน</x-data-table.th>
            <x-data-table.th align="center">มาสาย</x-data-table.th>
            <x-data-table.th align="center">กลับก่อน</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th>หมายเหตุ</x-data-table.th>
        </x-slot:head>

        @forelse ($summaries as $summary)
            <x-data-table.row>
                <x-data-table.td class="whitespace-nowrap tabular-nums">{{ $summary->work_date?->format('Y-m-d') }}</x-data-table.td>

                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $summary->employee?->employee_code }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $summary->employee?->full_name ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td>{{ $summary->employee?->department?->name ?? '-' }}</x-data-table.td>

                <x-data-table.td>
                    @if ($summary->dutySchedule)
                        <div>{{ $summary->dutySchedule->shiftType?->name ?? '-' }}</div>
                        <div class="mt-0.5 text-xs text-slate-500 tabular-nums">
                            {{ $summary->dutySchedule->start_at?->format('H:i') }} –
                            {{ $summary->dutySchedule->end_at?->format('H:i') }}
                        </div>
                    @else
                        <span class="text-slate-400">ไม่ได้ผูกตารางเวร</span>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center" class="whitespace-nowrap tabular-nums">
                    {{ $summary->first_in_at?->format('H:i:s') ?? '-' }}

                    @if ($summary->expected_in_time)
                        <div class="mt-0.5 text-xs text-slate-500">
                            กำหนด {{ \Carbon\Carbon::parse($summary->expected_in_time)->format('H:i') }}
                        </div>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center" class="whitespace-nowrap tabular-nums">
                    {{ $summary->last_out_at?->format('H:i:s') ?? '-' }}

                    @if ($summary->expected_out_time)
                        <div class="mt-0.5 text-xs text-slate-500">
                            กำหนด {{ \Carbon\Carbon::parse($summary->expected_out_time)->format('H:i') }}
                        </div>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">{{ $summary->work_hours }}</x-data-table.td>

                <x-data-table.td align="center">
                    @if ($summary->late_minutes > 0)
                        <span class="font-semibold text-amber-600">{{ $summary->late_minutes }} นาที</span>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">
                    @if ($summary->early_leave_minutes > 0)
                        <span class="font-semibold text-orange-600">{{ $summary->early_leave_minutes }} นาที</span>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">
                    @php
                        $tone = match ($summary->status) {
                            'normal' => 'success',
                            'late', 'early_leave' => 'warning',
                            'late_and_early_leave' => 'danger',
                            'absent' => 'danger',
                            default => 'slate',
                        };
                    @endphp

                    <x-badge :tone="$tone" dot>{{ $statuses[$summary->status] ?? $summary->status }}</x-badge>
                </x-data-table.td>

                <x-data-table.td class="text-xs text-slate-500">{{ $summary->remark ?? '-' }}</x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="11" icon="clock" title="ไม่พบสรุปเวลาทำงาน"
                                description="ลองเปลี่ยนช่วงวันที่ หรือสร้างสรุปเวลาจากข้อมูลสแกน">
                @can('attendance.import')
                    <x-btn :href="route('attendance-summaries.generate-form')">สร้างสรุปเวลา</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $summaries->links() }}</div>
</x-layouts.app>
