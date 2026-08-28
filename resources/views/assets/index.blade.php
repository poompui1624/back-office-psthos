<x-layouts.app title="ทะเบียนพัสดุ">
    @php
        $assetStatuses = [
            'active' => 'ใช้งาน',
            'repairing' => 'กำลังซ่อม',
            'broken' => 'ชำรุด',
            'disposed' => 'จำหน่าย',
            'lost' => 'สูญหาย',
        ];

        $assetTones = [
            'active' => 'success',
            'repairing' => 'warning',
            'broken' => 'danger',
            'disposed' => 'slate',
            'lost' => 'danger',
        ];
    @endphp

    <x-page-header title="ทะเบียนพัสดุ" subtitle="จัดการข้อมูลพัสดุรวมทั้งโรงพยาบาล">
        @can('asset.create')
            <x-btn :href="route('assets.create')" icon="box">เพิ่มพัสดุ</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('assets.index')">
        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="รหัส / ชื่อ / serial / หน่วยงาน" />
        </x-form.field>

        <x-form.field label="สถานะ">
            <x-form.select name="status" class="w-44">
                <option value="">ทุกสถานะ</option>

                @foreach ($assetStatuses as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>รหัสพัสดุ</x-data-table.th>
            <x-data-table.th>ชื่อพัสดุ</x-data-table.th>
            <x-data-table.th>หมวดหมู่</x-data-table.th>
            <x-data-table.th>หน่วยงาน</x-data-table.th>
            <x-data-table.th>ผู้รับผิดชอบ</x-data-table.th>
            <x-data-table.th align="center">อายุพัสดุ</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($assets as $asset)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $asset->asset_code }}</x-data-table.td>

                <x-data-table.td>
                    <div>{{ $asset->name }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $asset->brand }} {{ $asset->model }}</div>

                    @if ($asset->serial_number)
                        <div class="text-xs text-slate-500">S/N: {{ $asset->serial_number }}</div>
                    @endif
                </x-data-table.td>

                <x-data-table.td>{{ $asset->category?->name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $asset->department?->name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $asset->responsibleEmployee?->full_name ?? '-' }}</x-data-table.td>

                <x-data-table.td align="center">
                    <div class="font-medium text-slate-900">{{ $asset->age_text }}</div>

                    @if ($asset->received_date)
                        <div class="mt-0.5 text-xs text-slate-500">รับเข้า {{ $asset->received_date->format('d/m/Y') }}</div>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$assetTones[$asset->status] ?? 'slate'" dot>
                        {{ $assetStatuses[$asset->status] ?? $asset->status }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        @can('asset.movement')
                            <x-btn :href="route('asset-movements.create', ['asset_id' => $asset->id])" size="sm">โอนย้าย</x-btn>
                        @endcan

                        @can('asset.update')
                            <x-btn :href="route('assets.edit', $asset)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('asset.delete')
                            <form method="POST"
                                  action="{{ route('assets.destroy', $asset) }}"
                                  onsubmit="return confirm('ยืนยันการลบพัสดุนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="8" icon="box" title="ไม่พบข้อมูลพัสดุ"
                                description="ลองเปลี่ยนคำค้นหาหรือสถานะ">
                @can('asset.create')
                    <x-btn :href="route('assets.create')">เพิ่มพัสดุ</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $assets->links() }}</div>
</x-layouts.app>
