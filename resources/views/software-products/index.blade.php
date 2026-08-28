<x-layouts.app title="Software Products">
    <x-page-header title="Software Products" subtitle="ทะเบียนโปรแกรมและผู้ผลิต">
        @can('software.create')
            <x-btn :href="route('software-products.create')" icon="box">เพิ่ม Software</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('software-products.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search ?? ''" placeholder="ชื่อ Software / Vendor / Category" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ชื่อ Software</x-data-table.th>
            <x-data-table.th>Vendor</x-data-table.th>
            <x-data-table.th>Category</x-data-table.th>
            <x-data-table.th align="center">License</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($products as $product)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $product->name }}</x-data-table.td>
                <x-data-table.td>{{ $product->vendor ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $product->category ?? '-' }}</x-data-table.td>
                <x-data-table.td align="center">{{ $product->licenses_count }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$product->is_active ? 'success' : 'slate'" dot>
                        {{ $product->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex justify-center gap-2">
                        @can('software.update')
                            <x-btn :href="route('software-products.edit', $product)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('software.delete')
                            <form method="POST"
                                  action="{{ route('software-products.destroy', $product) }}"
                                  onsubmit="return confirm('ยืนยันการลบ Software นี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="6" icon="box" title="ไม่พบ Software"
                                description="เพิ่มโปรแกรมก่อนจึงจะบันทึก License ได้">
                @can('software.create')
                    <x-btn :href="route('software-products.create')">เพิ่ม Software</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $products->links() }}</div>
</x-layouts.app>
