<x-layouts.app title="ประวัติการโอนย้ายพัสดุ">
    <x-page-header title="ประวัติการโอนย้ายพัสดุ" subtitle="ติดตามการย้ายพัสดุระหว่างหน่วยงานและผู้รับผิดชอบ">
        @can('asset.movement')
            <x-btn :href="route('asset-movements.create')" icon="swap">โอนย้ายพัสดุ</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('asset-movements.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search" placeholder="รหัสพัสดุ / ชื่อพัสดุ / หน่วยงาน / เหตุผล" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>วันที่โอนย้าย</x-data-table.th>
            <x-data-table.th>พัสดุ</x-data-table.th>
            <x-data-table.th>จากหน่วยงาน</x-data-table.th>
            <x-data-table.th>ไปหน่วยงาน</x-data-table.th>
            <x-data-table.th>ผู้รับผิดชอบเดิม</x-data-table.th>
            <x-data-table.th>ผู้รับผิดชอบใหม่</x-data-table.th>
            <x-data-table.th>ผู้บันทึก</x-data-table.th>
            <x-data-table.th>เหตุผล</x-data-table.th>
        </x-slot:head>

        @forelse ($movements as $movement)
            <x-data-table.row>
                <x-data-table.td class="whitespace-nowrap tabular-nums">
                    {{ $movement->moved_at?->format('Y-m-d') }}
                </x-data-table.td>

                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $movement->asset?->asset_code }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $movement->asset?->name }}</div>
                </x-data-table.td>

                <x-data-table.td>{{ $movement->fromDepartment?->name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $movement->toDepartment?->name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $movement->fromEmployee?->full_name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $movement->toEmployee?->full_name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $movement->movedBy?->name ?? '-' }}</x-data-table.td>

                <x-data-table.td>
                    {{ $movement->reason ?? '-' }}

                    @if ($movement->remark)
                        <div class="mt-0.5 text-xs text-slate-500">{{ $movement->remark }}</div>
                    @endif
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="8" icon="swap" title="ไม่พบประวัติการโอนย้ายพัสดุ"
                                description="การโอนย้ายจะถูกบันทึกไว้ที่นี่ทุกครั้ง">
                @can('asset.movement')
                    <x-btn :href="route('asset-movements.create')">โอนย้ายพัสดุ</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $movements->links() }}</div>
</x-layouts.app>
