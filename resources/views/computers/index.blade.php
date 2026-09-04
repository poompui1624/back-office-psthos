<x-layouts.app title="ทะเบียนคอมพิวเตอร์">
    @php
        $computerStatuses = ['active' => 'ใช้งาน', 'inactive' => 'ปิดใช้งาน', 'repairing' => 'กำลังซ่อม', 'disposed' => 'จำหน่าย'];
        $computerTones = ['active' => 'success', 'inactive' => 'slate', 'repairing' => 'warning', 'disposed' => 'slate'];
    @endphp

    <x-page-header title="ทะเบียนคอมพิวเตอร์" subtitle="จัดการเครื่องคอมพิวเตอร์ภายในโรงพยาบาล">
        @can('computer.create')
            <x-btn :href="route('computers.create')" icon="device">เพิ่มคอมพิวเตอร์</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('computers.index')">
        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="hostname / IP / serial / หน่วยงาน" />
        </x-form.field>

        <x-form.field label="สถานะ">
            <x-form.select name="status" class="w-44">
                <option value="">ทุกสถานะ</option>

                @foreach ($computerStatuses as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>Hostname</x-data-table.th>
            <x-data-table.th>Asset</x-data-table.th>
            <x-data-table.th>IP / MAC</x-data-table.th>
            <x-data-table.th>OS</x-data-table.th>
            <x-data-table.th>หน่วยงาน</x-data-table.th>
            <x-data-table.th>ผู้รับผิดชอบ</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($computers as $computer)
            <x-data-table.row>
                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $computer->hostname }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $computer->manufacturer }} {{ $computer->model }}</div>

                    @if ($computer->serial_number)
                        <div class="text-xs text-slate-500">S/N: {{ $computer->serial_number }}</div>
                    @endif
                </x-data-table.td>

                <x-data-table.td>
                    @if ($computer->asset)
                        <div>{{ $computer->asset->asset_code }}</div>
                        <div class="mt-0.5 text-xs text-slate-500">{{ $computer->asset->name }}</div>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $computer->ip_address ?? '-' }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $computer->mac_address ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $computer->os_name ?? '-' }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $computer->os_version ?? '' }}</div>
                </x-data-table.td>

                <x-data-table.td>{{ $computer->department?->name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $computer->responsibleEmployee?->full_name ?? '-' }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$computerTones[$computer->status] ?? 'slate'" dot>
                        {{ $computerStatuses[$computer->status] ?? $computer->status }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        @can('computer.view')
                            <x-btn :href="route('computers.show', $computer)" variant="secondary" size="sm">รายละเอียด</x-btn>
                        @endcan

                        @can('computer.update')
                            <x-btn :href="route('computers.edit', $computer)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('computer.delete')
                            <form method="POST"
                                  action="{{ route('computers.destroy', $computer) }}"
                                  onsubmit="return confirm('ยืนยันการลบเครื่องนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="8" icon="device" title="ไม่พบข้อมูลคอมพิวเตอร์"
                                description="ลองเปลี่ยนคำค้นหาหรือสถานะ">
                @can('computer.create')
                    <x-btn :href="route('computers.create')">เพิ่มคอมพิวเตอร์</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $computers->links() }}</div>
</x-layouts.app>
