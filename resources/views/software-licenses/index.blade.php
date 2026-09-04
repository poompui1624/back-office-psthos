<x-layouts.app title="Software Licenses">
    @php
        $licenseStatuses = ['active' => 'ใช้งาน', 'expired' => 'หมดอายุ', 'renewed' => 'ต่ออายุแล้ว', 'cancelled' => 'ยกเลิก'];
        $licenseTones = ['active' => 'success', 'expired' => 'danger', 'renewed' => 'info', 'cancelled' => 'slate'];
    @endphp

    <x-page-header title="Software Licenses" subtitle="ทะเบียน License วันหมดอายุ ต่ออายุ และยกเลิก">
        @can('software.create')
            <x-btn :href="route('software-licenses.create')" icon="key">เพิ่ม License</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('software-licenses.index')">
        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="Software / License / Vendor" />
        </x-form.field>

        <x-form.field label="สถานะ">
            <x-form.select name="status" class="w-44">
                <option value="">ทุกสถานะ</option>

                @foreach ($licenseStatuses as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>Software</x-data-table.th>
            <x-data-table.th>License</x-data-table.th>
            <x-data-table.th align="center">Seats</x-data-table.th>
            <x-data-table.th>วันหมดอายุ</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($licenses as $license)
            <x-data-table.row>
                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $license->product?->name }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $license->product?->vendor ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $license->license_name ?? '-' }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $license->license_type ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">
                    {{ $license->used_seats }} / {{ $license->total_seats }}
                </x-data-table.td>

                <x-data-table.td>
                    @if ($license->expire_date)
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="tabular-nums">{{ $license->expire_date->format('Y-m-d') }}</span>

                            @if ($license->is_expired)
                                <x-badge tone="danger">หมดอายุ</x-badge>
                            @elseif ($license->is_expiring_soon)
                                <x-badge tone="warning">ใกล้หมดอายุ</x-badge>
                            @endif
                        </div>

                        @if ($license->last_expire_notified_at)
                            <div class="mt-1 text-xs text-slate-500">
                                แจ้งเตือนล่าสุด: {{ $license->last_expire_notified_at->format('Y-m-d H:i') }}
                            </div>
                        @endif
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$licenseTones[$license->status] ?? 'slate'" dot>
                        {{ $licenseStatuses[$license->status] ?? $license->status }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        @can('software.update')
                            <x-btn :href="route('software-licenses.renew-form', $license)" size="sm">ต่ออายุ</x-btn>
                        @endcan

                        @if ($license->status !== 'cancelled')
                            <x-btn :href="route('software-licenses.cancel-form', $license)" variant="danger" size="sm">ยกเลิก</x-btn>
                        @endif

                        @can('software.update')
                            <x-btn :href="route('software-licenses.edit', $license)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="6" icon="key" title="ไม่พบ License"
                                description="ลองเปลี่ยนคำค้นหาหรือสถานะ">
                @can('software.create')
                    <x-btn :href="route('software-licenses.create')">เพิ่ม License</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $licenses->links() }}</div>
</x-layouts.app>
