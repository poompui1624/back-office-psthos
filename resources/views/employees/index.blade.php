<x-layouts.app title="ทะเบียนบุคลากร">
    <x-page-header title="ทะเบียนบุคลากร" subtitle="จัดการข้อมูลบุคลากร หน่วยงาน และตำแหน่ง">
        @can('employee.create')
            <x-btn :href="route('employees.create')" icon="users">เพิ่มบุคลากร</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('employees.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search" placeholder="รหัส / ชื่อ / หน่วยงาน / ตำแหน่ง / เบอร์โทร" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>รหัส</x-data-table.th>
            <x-data-table.th>ชื่อ-สกุล</x-data-table.th>
            <x-data-table.th>หน่วยงาน</x-data-table.th>
            <x-data-table.th>ตำแหน่ง</x-data-table.th>
            <x-data-table.th>โทรศัพท์</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($employees as $employee)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $employee->employee_code }}</x-data-table.td>
                <x-data-table.td>{{ $employee->full_name }}</x-data-table.td>
                <x-data-table.td>{{ $employee->department?->name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $employee->position?->name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $employee->phone ?? '-' }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$employee->status === 'active' ? 'success' : 'slate'" dot>
                        {{ $employee->status === 'active' ? 'ปฏิบัติงาน' : $employee->status }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex justify-center gap-2">
                        @can('employee.update')
                            <x-btn :href="route('employees.edit', $employee)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @if (auth()->user()?->can('employee.sensitive.view') || auth()->user()?->can('employee.update'))
                            <x-btn :href="route('employees.personnel-profile.edit', $employee)" size="sm">ก.พ.7</x-btn>
                        @endif

                        @can('employee.delete')
                            <form method="POST"
                                  action="{{ route('employees.destroy', $employee) }}"
                                  onsubmit="return confirm('ยืนยันการลบบุคลากรนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="users" title="ไม่พบข้อมูลบุคลากร"
                                description="ลองเปลี่ยนคำค้นหา หรือเพิ่มบุคลากรใหม่">
                @can('employee.create')
                    <x-btn :href="route('employees.create')">เพิ่มบุคลากร</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $employees->links() }}</div>
</x-layouts.app>
