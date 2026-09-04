<x-layouts.app title="ผู้บริหาร">
    @include('site.admin._nav')

    <x-page-header title="ผู้บริหาร" subtitle="รายนามผู้บริหารที่แสดงบนหน้าเว็บ">
        @can('site.manage')
            <x-btn :href="route('site.executives.create')" icon="users">เพิ่มผู้บริหาร</x-btn>
        @endcan
    </x-page-header>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ชื่อ</x-data-table.th>
            <x-data-table.th>ตำแหน่ง</x-data-table.th>
            <x-data-table.th>ติดต่อ</x-data-table.th>
            <x-data-table.th align="center">ลำดับ</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($executives as $executive)
            <x-data-table.row>
                <x-data-table.td>
                    <div class="flex items-center gap-3">
                        @if ($executive->photo_url)
                            <img src="{{ $executive->photo_url }}" alt=""
                                 class="h-11 w-9 shrink-0 rounded-lg object-cover ring-1 ring-slate-200">
                        @else
                            <span class="flex h-11 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                <x-icon name="user" class="h-4 w-4" />
                            </span>
                        @endif

                        <div>
                            <div class="font-medium text-slate-900">{{ $executive->name }}</div>

                            @if ($executive->is_featured)
                                <div class="mt-1"><x-badge tone="brand">แสดงเด่นหน้าแรก</x-badge></div>
                            @endif
                        </div>
                    </div>
                </x-data-table.td>

                <x-data-table.td>{{ $executive->position ?: '-' }}</x-data-table.td>

                <x-data-table.td class="text-xs">
                    <div>{{ $executive->phone ?: '-' }}</div>

                    @if ($executive->email)
                        <div class="mt-0.5 text-slate-500">{{ $executive->email }}</div>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">{{ $executive->sort_order }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$executive->is_active ? 'success' : 'slate'" dot>
                        {{ $executive->is_active ? 'แสดง' : 'ซ่อน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    @can('site.manage')
                        <div class="flex justify-center gap-2">
                            <x-btn :href="route('site.executives.edit', $executive)" variant="secondary" size="sm">แก้ไข</x-btn>

                            <form method="POST" action="{{ route('site.executives.destroy', $executive) }}"
                                  onsubmit="return confirm('ยืนยันการลบรายชื่อนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        </div>
                    @endcan
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="6" icon="users" title="ยังไม่มีรายชื่อผู้บริหาร"
                                description="เพิ่มผู้อำนวยการและทีมบริหารเพื่อแสดงบนหน้าเว็บ">
                @can('site.manage')
                    <x-btn :href="route('site.executives.create')">เพิ่มผู้บริหาร</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $executives->links() }}</div>
</x-layouts.app>
