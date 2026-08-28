<x-layouts.app title="ห้องประชุม">
    <x-page-header title="ห้องประชุม" subtitle="จัดการทะเบียนห้องประชุมของโรงพยาบาล">
        <x-btn :href="route('meeting-bookings.index')" variant="secondary" icon="calendar">รายการจอง</x-btn>

        @can('meeting.create')
            <x-btn :href="route('meeting-rooms.create')" icon="building">เพิ่มห้องประชุม</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('meeting-rooms.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search ?? ''" placeholder="รหัส / ชื่อห้อง / สถานที่" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>รหัส</x-data-table.th>
            <x-data-table.th>ชื่อห้อง</x-data-table.th>
            <x-data-table.th>สถานที่</x-data-table.th>
            <x-data-table.th align="center">ความจุ</x-data-table.th>
            <x-data-table.th>อุปกรณ์</x-data-table.th>
            <x-data-table.th align="center">จำนวนจอง</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($rooms as $room)
            @php
                $facilities = collect([
                    'โปรเจกเตอร์' => $room->has_projector,
                    'เครื่องเสียง' => $room->has_sound_system,
                    'ประชุมทางไกล' => $room->has_video_conference,
                    'ไวท์บอร์ด' => $room->has_whiteboard,
                ])->filter()->keys();
            @endphp

            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $room->code }}</x-data-table.td>

                <x-data-table.td>
                    <div>{{ $room->name }}</div>

                    @if ($room->description)
                        <div class="mt-0.5 text-xs text-slate-500">{{ $room->description }}</div>
                    @endif
                </x-data-table.td>

                <x-data-table.td>{{ $room->location ?? '-' }}</x-data-table.td>
                <x-data-table.td align="center">{{ $room->capacity }}</x-data-table.td>

                <x-data-table.td>
                    @if ($facilities->isEmpty())
                        <span class="text-slate-400">-</span>
                    @else
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($facilities as $facility)
                                <x-badge tone="info">{{ $facility }}</x-badge>
                            @endforeach
                        </div>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">{{ $room->bookings_count }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$room->is_active ? 'success' : 'slate'" dot>
                        {{ $room->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex justify-center gap-2">
                        @can('meeting.update')
                            <x-btn :href="route('meeting-rooms.edit', $room)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('meeting.delete')
                            <form method="POST"
                                  action="{{ route('meeting-rooms.destroy', $room) }}"
                                  onsubmit="return confirm('ยืนยันการลบห้องประชุมนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="8" icon="building" title="ไม่พบห้องประชุม"
                                description="เพิ่มห้องประชุมก่อนจึงจะเปิดให้จองได้">
                @can('meeting.create')
                    <x-btn :href="route('meeting-rooms.create')">เพิ่มห้องประชุม</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $rooms->links() }}</div>
</x-layouts.app>
