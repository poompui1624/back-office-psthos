<x-layouts.app title="ระบบการลา">
    <x-page-header title="ระบบการลา" subtitle="รายการคำขอลาของบุคลากร">
        <x-btn :href="route('leave-requests.dashboard')" variant="secondary" icon="chart">Dashboard</x-btn>
        <x-btn :href="route('leave-requests.calendar')" variant="secondary" icon="calendar">ปฏิทินการลา</x-btn>

        @can('leave.create')
            <x-btn :href="route('leave-requests.create')" icon="calendar">ยื่นคำขอลา</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('leave-requests.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search" placeholder="เลขคำขอ / ชื่อบุคลากร / ประเภทลา" />
        </x-form.field>

        <x-form.field label="สถานะ">
            @php
                $statuses = ['pending' => 'รออนุมัติ', 'approved' => 'อนุมัติแล้ว', 'rejected' => 'ไม่อนุมัติ', 'cancelled' => 'ยกเลิก'];
            @endphp

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
            <x-data-table.th>เลขคำขอ</x-data-table.th>
            <x-data-table.th>บุคลากร</x-data-table.th>
            <x-data-table.th>ประเภทการลา</x-data-table.th>
            <x-data-table.th>ช่วงวันที่</x-data-table.th>
            <x-data-table.th align="center">จำนวนวัน</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th>ผู้อนุมัติ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($leaveRequests as $leaveRequest)
            <x-data-table.row>
                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $leaveRequest->request_no }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $leaveRequest->created_at->format('Y-m-d H:i') }}</div>
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $leaveRequest->employee?->full_name ?? '-' }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $leaveRequest->department?->name ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td>{{ $leaveRequest->leaveType?->name ?? '-' }}</x-data-table.td>

                <x-data-table.td class="tabular-nums">
                    {{ $leaveRequest->start_date?->format('Y-m-d') }}
                    <span class="text-slate-400">–</span>
                    {{ $leaveRequest->end_date?->format('Y-m-d') }}
                </x-data-table.td>

                <x-data-table.td align="center">{{ $leaveRequest->total_days }}</x-data-table.td>

                <x-data-table.td align="center">
                    @php
                        $tone = match ($leaveRequest->status) {
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'cancelled' => 'slate',
                            default => 'slate',
                        };

                        $label = match ($leaveRequest->status) {
                            'pending' => 'รออนุมัติ',
                            'approved' => 'อนุมัติแล้ว',
                            'rejected' => 'ไม่อนุมัติ',
                            'cancelled' => 'ยกเลิก',
                            default => $leaveRequest->status,
                        };
                    @endphp

                    <x-badge :tone="$tone" dot>{{ $label }}</x-badge>
                </x-data-table.td>

                <x-data-table.td>{{ $leaveRequest->approver?->name ?? '-' }}</x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-btn :href="route('leave-requests.show', $leaveRequest)" variant="secondary" size="sm">
                            รายละเอียด
                        </x-btn>

                        @if ($leaveRequest->isPending())
                            @can('leave.update')
                                <x-btn :href="route('leave-requests.edit', $leaveRequest)" variant="warning" size="sm">
                                    แก้ไข
                                </x-btn>
                            @endcan

                            @can('leave.delete')
                                <form method="POST"
                                      action="{{ route('leave-requests.destroy', $leaveRequest) }}"
                                      onsubmit="return confirm('ยืนยันการลบคำขอนี้?')">
                                    @csrf
                                    @method('DELETE')

                                    <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                                </form>
                            @endcan
                        @endif
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="8" icon="calendar" title="ไม่พบคำขอลา"
                                description="ลองเปลี่ยนคำค้นหาหรือสถานะ">
                @can('leave.create')
                    <x-btn :href="route('leave-requests.create')">ยื่นคำขอลา</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $leaveRequests->links() }}</div>
</x-layouts.app>
