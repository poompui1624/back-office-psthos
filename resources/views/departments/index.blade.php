<x-layouts.app title="ทะเบียนหน่วยงาน">
    <x-page-header title="ทะเบียนหน่วยงาน" subtitle="จัดการหน่วยงาน กลุ่มงาน และแผนกภายในโรงพยาบาล">
        @can('department.create')
            <x-btn :href="route('departments.create')" icon="building">เพิ่มหน่วยงาน</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('departments.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search" placeholder="รหัส / ชื่อหน่วยงาน / ประเภท" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>รหัส</x-data-table.th>
            <x-data-table.th>ชื่อหน่วยงาน</x-data-table.th>
            <x-data-table.th>หน่วยงานแม่</x-data-table.th>
            <x-data-table.th>ประเภท</x-data-table.th>
            <x-data-table.th align="center">บุคลากร</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($departments as $department)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $department->code }}</x-data-table.td>
                <x-data-table.td>{{ $department->name }}</x-data-table.td>
                <x-data-table.td>{{ $department->parent?->name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $department->type ?? '-' }}</x-data-table.td>
                <x-data-table.td align="center">{{ $department->employees_count }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$department->is_active ? 'success' : 'slate'" dot>
                        {{ $department->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex justify-center gap-2">
                        @can('department.update')
                            <x-btn :href="route('departments.edit', $department)" variant="secondary" size="sm">
                                แก้ไข
                            </x-btn>
                        @endcan

                        @can('department.delete')
                            <form method="POST"
                                  action="{{ route('departments.destroy', $department) }}"
                                  onsubmit="return confirm('ยืนยันการลบหน่วยงานนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="building" title="ไม่พบข้อมูลหน่วยงาน"
                                description="ลองเปลี่ยนคำค้นหา หรือเพิ่มหน่วยงานใหม่">
                @can('department.create')
                    <x-btn :href="route('departments.create')">เพิ่มหน่วยงาน</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $departments->links() }}</div>
</x-layouts.app>
