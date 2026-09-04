<x-layouts.app title="ลิงก์สำคัญ">
    @include('site.admin._nav')

    <x-page-header title="ลิงก์สำคัญ" subtitle="ปุ่มลัดที่แสดงเป็นตารางไอคอนบนหน้าเว็บ">
        @can('site.manage')
            <x-btn :href="route('site.links.create')" icon="external-link">เพิ่มลิงก์</x-btn>
        @endcan
    </x-page-header>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ชื่อลิงก์</x-data-table.th>
            <x-data-table.th>ปลายทาง</x-data-table.th>
            <x-data-table.th align="center">เปิดแท็บใหม่</x-data-table.th>
            <x-data-table.th align="center">ลำดับ</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($links as $link)
            <x-data-table.row>
                <x-data-table.td>
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                            <x-icon :name="$link->icon ?: 'document'" class="h-4 w-4" />
                        </span>

                        <div>
                            <div class="font-medium text-slate-900">{{ $link->label }}</div>

                            @if ($link->description)
                                <div class="mt-0.5 text-xs text-slate-500">{{ $link->description }}</div>
                            @endif
                        </div>
                    </div>
                </x-data-table.td>

                <x-data-table.td>
                    <div class="max-w-xs truncate text-xs text-slate-600">{{ $link->url }}</div>
                </x-data-table.td>

                <x-data-table.td align="center">
                    {{ $link->opens_new_tab ? 'ใช่' : 'ไม่' }}
                </x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">{{ $link->sort_order }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$link->is_active ? 'success' : 'slate'" dot>
                        {{ $link->is_active ? 'แสดง' : 'ซ่อน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    @can('site.manage')
                        <div class="flex justify-center gap-2">
                            <x-btn :href="route('site.links.edit', $link)" variant="secondary" size="sm">แก้ไข</x-btn>

                            <form method="POST" action="{{ route('site.links.destroy', $link) }}"
                                  onsubmit="return confirm('ยืนยันการลบลิงก์นี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        </div>
                    @endcan
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="6" icon="external-link" title="ยังไม่มีลิงก์สำคัญ"
                                description="เพิ่มลิงก์ไปยังบริการหรือระบบที่ผู้มารับบริการใช้บ่อย">
                @can('site.manage')
                    <x-btn :href="route('site.links.create')">เพิ่มลิงก์</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $links->links() }}</div>
</x-layouts.app>
