<x-layouts.app title="หมวดหมู่พัสดุ">
    <x-page-header title="หมวดหมู่พัสดุ" subtitle="จัดการประเภทของพัสดุ">
        @can('asset.create')
            <x-btn :href="route('asset-categories.create')" icon="clipboard">เพิ่มหมวดหมู่</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('asset-categories.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search" placeholder="รหัส / ชื่อหมวดหมู่" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>รหัส</x-data-table.th>
            <x-data-table.th>ชื่อหมวดหมู่</x-data-table.th>
            <x-data-table.th align="center">จำนวนพัสดุ</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($categories as $category)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $category->code }}</x-data-table.td>
                <x-data-table.td>{{ $category->name }}</x-data-table.td>
                <x-data-table.td align="center">{{ $category->assets_count }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$category->is_active ? 'success' : 'slate'" dot>
                        {{ $category->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex justify-center gap-2">
                        @can('asset.update')
                            <x-btn :href="route('asset-categories.edit', $category)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('asset.delete')
                            <form method="POST"
                                  action="{{ route('asset-categories.destroy', $category) }}"
                                  onsubmit="return confirm('ยืนยันการลบหมวดหมู่นี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="5" icon="clipboard" title="ไม่พบหมวดหมู่พัสดุ"
                                description="เพิ่มหมวดหมู่ก่อนจึงจะบันทึกพัสดุได้">
                @can('asset.create')
                    <x-btn :href="route('asset-categories.create')">เพิ่มหมวดหมู่</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-layouts.app>
