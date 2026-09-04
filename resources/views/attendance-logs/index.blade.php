<x-layouts.app title="ข้อมูลเวลาสแกน">
    <x-page-header title="ข้อมูลเวลาสแกน" subtitle="รายการเวลาที่นำเข้าจากเครื่องสแกนนิ้วมือ">
        @can('attendance.view')
            <x-btn :href="route('attendance-devices.index')" variant="secondary" icon="device">เครื่องสแกน</x-btn>
        @endcan

        @can('attendance.import')
            <x-btn :href="route('attendance-logs.import-form')" icon="box">นำเข้า CSV</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('attendance-logs.index')">
        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="รหัสพนักงาน / ชื่อ / เครื่องสแกน" />
        </x-form.field>

        <x-form.field label="ตั้งแต่วันที่">
            <x-form.input type="date" name="date_from" :value="$dateFrom" />
        </x-form.field>

        <x-form.field label="ถึงวันที่">
            <x-form.input type="date" name="date_to" :value="$dateTo" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>วันเวลา</x-data-table.th>
            <x-data-table.th>รหัสพนักงาน</x-data-table.th>
            <x-data-table.th>บุคลากร</x-data-table.th>
            <x-data-table.th>เครื่องสแกน</x-data-table.th>
            <x-data-table.th>ประเภท</x-data-table.th>
            <x-data-table.th>Verify</x-data-table.th>
            <x-data-table.th>Source</x-data-table.th>
        </x-slot:head>

        @forelse ($logs as $log)
            <x-data-table.row>
                <x-data-table.td class="whitespace-nowrap">
                    <div class="font-medium text-slate-900 tabular-nums">{{ $log->scan_time?->format('Y-m-d H:i:s') }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">วันที่: {{ $log->scan_date?->format('Y-m-d') }}</div>
                </x-data-table.td>

                <x-data-table.td>{{ $log->employee_code ?? '-' }}</x-data-table.td>

                <x-data-table.td>
                    @if ($log->employee)
                        <div>{{ $log->employee->full_name }}</div>
                        <div class="mt-0.5 text-xs text-slate-500">{{ $log->employee->department?->name ?? '-' }}</div>
                    @else
                        <x-badge tone="danger">ไม่พบในทะเบียนบุคลากร</x-badge>
                    @endif
                </x-data-table.td>

                <x-data-table.td>
                    @if ($log->device)
                        <div>{{ $log->device->code }}</div>
                        <div class="mt-0.5 text-xs text-slate-500">{{ $log->device->name }}</div>
                    @else
                        {{ $log->device_code ?? '-' }}
                    @endif
                </x-data-table.td>

                <x-data-table.td>{{ $log->scan_type ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $log->verify_type ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $log->source }}</x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="clock" title="ไม่พบข้อมูลเวลาสแกน"
                                description="ลองเปลี่ยนช่วงวันที่ หรือนำเข้าไฟล์จากเครื่องสแกน">
                @can('attendance.import')
                    <x-btn :href="route('attendance-logs.import-form')">นำเข้า CSV</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $logs->links() }}</div>
</x-layouts.app>
