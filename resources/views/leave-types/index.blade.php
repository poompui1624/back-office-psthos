<x-layouts.app title="ประเภทการลา">
    <x-page-header title="ประเภทการลา" subtitle="กำหนดประเภทการลาและจำนวนวันต่อปี">
        @can('leave.create')
            <x-btn :href="route('leave-types.create')" icon="clipboard">เพิ่มประเภทการลา</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('leave-types.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search ?? ''" placeholder="รหัส / ชื่อประเภทการลา" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>รหัส</x-data-table.th>
            <x-data-table.th>ชื่อประเภทการลา</x-data-table.th>
            <x-data-table.th align="center">วันต่อปี</x-data-table.th>
            <x-data-table.th align="center">ต้องแนบเอกสาร</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($leaveTypes as $leaveType)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $leaveType->code }}</x-data-table.td>
                <x-data-table.td>{{ $leaveType->name }}</x-data-table.td>

                <x-data-table.td align="center">
                    {{ $leaveType->default_days_per_year ? rtrim(rtrim(number_format((float) $leaveType->default_days_per_year, 1), '0'), '.') : '-' }}
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$leaveType->requires_document ? 'warning' : 'slate'">
                        {{ $leaveType->requires_document ? 'ต้องแนบ' : 'ไม่ต้อง' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$leaveType->is_active ? 'success' : 'slate'" dot>
                        {{ $leaveType->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex justify-center gap-2">
                        @can('leave.update')
                            <x-btn :href="route('leave-types.edit', $leaveType)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('leave.delete')
                            <form method="POST"
                                  action="{{ route('leave-types.destroy', $leaveType) }}"
                                  onsubmit="return confirm('ยืนยันการลบประเภทการลานี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="6" icon="clipboard" title="ไม่พบประเภทการลา"
                                description="เพิ่มประเภทการลาเพื่อให้บุคลากรเลือกตอนยื่นใบลา">
                @can('leave.create')
                    <x-btn :href="route('leave-types.create')">เพิ่มประเภทการลา</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $leaveTypes->links() }}</div>
</x-layouts.app>
