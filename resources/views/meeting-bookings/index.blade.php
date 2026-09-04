<x-layouts.app title="จองห้องประชุม">
    @php
        $statusLabels = ['pending' => 'รออนุมัติ', 'approved' => 'อนุมัติแล้ว', 'rejected' => 'ไม่อนุมัติ', 'cancelled' => 'ยกเลิก'];
        $statusTones = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'cancelled' => 'slate'];
    @endphp

    <x-page-header title="จองห้องประชุม" subtitle="รายการจองห้องประชุมทั้งหมด">
        <x-btn :href="route('meeting-rooms.index')" variant="secondary" icon="building">ห้องประชุม</x-btn>

        @can('meeting.create')
            <x-btn :href="route('meeting-bookings.create')" icon="calendar">จองห้องประชุม</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('meeting-bookings.index')">
        <x-form.field label="ค้นหา" class="min-w-56 flex-1">
            <x-form.input name="search" :value="$search" placeholder="เลขจอง / หัวข้อ / ผู้จอง" />
        </x-form.field>

        <x-form.field label="ห้องประชุม">
            <x-form.select name="meeting_room_id" class="w-48">
                <option value="">ทุกห้อง</option>

                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected($roomId == $room->id)>{{ $room->name }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>

        <x-form.field label="สถานะ">
            <x-form.select name="status" class="w-44">
                <option value="">ทุกสถานะ</option>

                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>เลขจอง</x-data-table.th>
            <x-data-table.th>หัวข้อ</x-data-table.th>
            <x-data-table.th>ห้อง</x-data-table.th>
            <x-data-table.th>ผู้จอง</x-data-table.th>
            <x-data-table.th align="center">วันเวลา</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($bookings as $booking)
            <x-data-table.row>
                <x-data-table.td class="whitespace-nowrap font-medium text-slate-900">
                    {{ $booking->booking_no }}
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $booking->title }}</div>

                    @if ($booking->purpose)
                        <div class="mt-0.5 text-xs text-slate-500">{{ Str::limit($booking->purpose, 80) }}</div>
                    @endif
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $booking->room?->name ?? '-' }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $booking->room?->location ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $booking->employee?->full_name ?? $booking->creator?->name ?? '-' }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $booking->department?->name ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td align="center" class="whitespace-nowrap tabular-nums">
                    <div>{{ $booking->start_at?->format('Y-m-d H:i') }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">ถึง {{ $booking->end_at?->format('Y-m-d H:i') }}</div>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$statusTones[$booking->status] ?? 'slate'" dot>
                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-btn :href="route('meeting-bookings.show', $booking)" variant="secondary" size="sm">
                            รายละเอียด
                        </x-btn>

                        @if ($booking->isPending())
                            @can('meeting.update')
                                <x-btn :href="route('meeting-bookings.edit', $booking)" variant="secondary" size="sm">แก้ไข</x-btn>
                            @endcan

                            @can('meeting.delete')
                                <form method="POST"
                                      action="{{ route('meeting-bookings.destroy', $booking) }}"
                                      onsubmit="return confirm('ยืนยันการลบรายการจองนี้?')">
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
            <x-data-table.empty :colspan="7" icon="calendar" title="ยังไม่มีรายการจองห้องประชุม"
                                description="ลองเปลี่ยนคำค้นหา ห้อง หรือสถานะ">
                @can('meeting.create')
                    <x-btn :href="route('meeting-bookings.create')">จองห้องประชุม</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $bookings->links() }}</div>
</x-layouts.app>
