<x-layouts.app title="Software Inventory">
    <x-page-header title="Software Inventory"
                   subtitle="รายงานโปรแกรมที่ติดตั้งในเครื่องคอมพิวเตอร์จากข้อมูล Agent ล่าสุด" />

    <x-filter-bar :action="route('software-inventory.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search" placeholder="ชื่อโปรแกรม / version / publisher" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ชื่อ Software</x-data-table.th>
            <x-data-table.th>Version</x-data-table.th>
            <x-data-table.th>Publisher</x-data-table.th>
            <x-data-table.th align="center">จำนวนเครื่อง</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($softwareItems as $software)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $software['name'] }}</x-data-table.td>
                <x-data-table.td>{{ $software['version'] ?: '-' }}</x-data-table.td>
                <x-data-table.td>{{ $software['publisher'] ?: '-' }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge tone="brand">{{ $software['computer_count'] }}</x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-btn variant="secondary" size="sm"
                           :href="route('software-inventory.computers', [
                               'name' => $software['name'],
                               'version' => $software['version'],
                               'publisher' => $software['publisher'],
                           ])">
                        ดูเครื่องที่ติดตั้ง
                    </x-btn>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="5" icon="box" title="ไม่พบข้อมูล Software Inventory"
                                description="ข้อมูลจะปรากฏเมื่อ Agent ส่งรายการโปรแกรมเข้ามา" />
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $softwareItems->links() }}</div>
</x-layouts.app>
