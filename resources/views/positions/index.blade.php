<x-layouts.app title="ทะเบียนตำแหน่ง">
    <x-page-header title="ทะเบียนตำแหน่ง" subtitle="กำหนดตำแหน่งงานและระดับสำหรับบุคลากร">
        @can('position.create')
            <x-btn :href="route('positions.create')" icon="clipboard">เพิ่มตำแหน่ง</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('positions.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search" placeholder="ชื่อตำแหน่ง / ระดับ" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ชื่อตำแหน่ง</x-data-table.th>
            <x-data-table.th>ระดับ</x-data-table.th>
            <x-data-table.th align="center">จำนวนบุคลากร</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($positions as $position)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $position->name }}</x-data-table.td>
                <x-data-table.td>{{ $position->level ?? '-' }}</x-data-table.td>
                <x-data-table.td align="center">{{ $position->employees_count }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$position->is_active ? 'success' : 'slate'" dot>
                        {{ $position->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex justify-center gap-2">
                        @can('position.update')
                            <x-btn :href="route('positions.edit', $position)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('position.delete')
                            <form method="POST"
                                  action="{{ route('positions.destroy', $position) }}"
                                  onsubmit="return confirm('ยืนยันการลบตำแหน่งนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="5" icon="clipboard" title="ไม่พบข้อมูลตำแหน่ง"
                                description="ลองเปลี่ยนคำค้นหา หรือเพิ่มตำแหน่งใหม่">
                @can('position.create')
                    <x-btn :href="route('positions.create')">เพิ่มตำแหน่ง</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $positions->links() }}</div>
</x-layouts.app>
