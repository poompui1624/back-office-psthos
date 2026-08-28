<x-layouts.app title="ประเภทเวร">
    <x-page-header title="ประเภทเวร" subtitle="กำหนดช่วงเวลาเวร สี และอัตราล่วงเวลา">
        @can('duty.create')
            <x-btn :href="route('shift-types.create')" icon="clock">เพิ่มประเภทเวร</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('shift-types.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search ?? ''" placeholder="รหัส / ชื่อเวร" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>รหัส</x-data-table.th>
            <x-data-table.th>ชื่อเวร</x-data-table.th>
            <x-data-table.th align="center">เวลา</x-data-table.th>
            <x-data-table.th align="center">ข้ามวัน</x-data-table.th>
            <x-data-table.th align="center">OT</x-data-table.th>
            <x-data-table.th align="center">จำนวนตารางเวร</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($shiftTypes as $shiftType)
            <x-data-table.row>
                <x-data-table.td>
                    @php
                        // Written out rather than interpolated: Tailwind scans source
                        // statically, so bg-{$colour}-500 would never be generated.
                        $swatches = [
                            'blue' => 'bg-blue-500', 'green' => 'bg-emerald-500', 'yellow' => 'bg-amber-500',
                            'purple' => 'bg-violet-500', 'red' => 'bg-rose-500', 'gray' => 'bg-slate-400',
                        ];
                    @endphp

                    <span class="inline-flex items-center gap-2 font-medium text-slate-900">
                        @if ($swatch = $swatches[$shiftType->color] ?? null)
                            <span class="h-2.5 w-2.5 rounded-full {{ $swatch }}"></span>
                        @endif

                        {{ $shiftType->code }}
                    </span>
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $shiftType->name }}</div>

                    @if ($shiftType->description)
                        <div class="mt-0.5 text-xs text-slate-500">{{ $shiftType->description }}</div>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">
                    {{ substr($shiftType->start_time, 0, 5) }} – {{ substr($shiftType->end_time, 0, 5) }}
                </x-data-table.td>

                <x-data-table.td align="center">
                    @if ($shiftType->crosses_midnight)
                        <x-badge tone="violet">ข้ามวัน</x-badge>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">
                    @if ($shiftType->is_ot)
                        <x-badge tone="warning">
                            {{ $shiftType->ot_flat_rate !== null
                                ? 'เหมา ' . number_format((float) $shiftType->ot_flat_rate)
                                : 'x' . rtrim(rtrim(number_format((float) $shiftType->ot_multiplier, 2), '0'), '.') }}
                        </x-badge>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">{{ $shiftType->duty_schedules_count }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$shiftType->is_active ? 'success' : 'slate'" dot>
                        {{ $shiftType->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex justify-center gap-2">
                        @can('duty.update')
                            <x-btn :href="route('shift-types.edit', $shiftType)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('duty.delete')
                            <form method="POST"
                                  action="{{ route('shift-types.destroy', $shiftType) }}"
                                  onsubmit="return confirm('ยืนยันการลบประเภทเวรนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="8" icon="clock" title="ไม่พบประเภทเวร"
                                description="เพิ่มประเภทเวรก่อนจึงจะจัดตารางเวรได้">
                @can('duty.create')
                    <x-btn :href="route('shift-types.create')">เพิ่มประเภทเวร</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $shiftTypes->links() }}</div>
</x-layouts.app>
