<x-layouts.app title="เครื่องที่ติดตั้ง Software">
    @php
        $detail = collect([
            $name,
            $version ? 'Version: ' . $version : null,
            $publisher ? 'Publisher: ' . $publisher : null,
        ])->filter()->implode(' / ');
    @endphp

    <x-page-header title="เครื่องที่ติดตั้ง Software" :subtitle="$detail">
        <x-btn :href="route('software-inventory.index')" variant="secondary">ย้อนกลับ</x-btn>
    </x-page-header>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>Hostname</x-data-table.th>
            <x-data-table.th>IP Address</x-data-table.th>
            <x-data-table.th>OS</x-data-table.th>
            <x-data-table.th>หน่วยงาน</x-data-table.th>
            <x-data-table.th>ผู้รับผิดชอบ</x-data-table.th>
            <x-data-table.th>Last Seen</x-data-table.th>
            <x-data-table.th align="center">รายละเอียด</x-data-table.th>
        </x-slot:head>

        @forelse ($computers as $computer)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $computer->hostname }}</x-data-table.td>
                <x-data-table.td>{{ $computer->ip_address ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $computer->os_name ?? '-' }} {{ $computer->os_version ?? '' }}</x-data-table.td>
                <x-data-table.td>{{ $computer->department?->name ?? '-' }}</x-data-table.td>
                <x-data-table.td>{{ $computer->responsibleEmployee?->full_name ?? '-' }}</x-data-table.td>

                <x-data-table.td class="tabular-nums">
                    {{ $computer->last_seen_at?->format('Y-m-d H:i') ?? '-' }}
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-btn :href="route('computers.show', $computer)" variant="secondary" size="sm">เปิด</x-btn>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="device" title="ไม่พบเครื่องที่ติดตั้ง Software นี้"
                                description="ข้อมูลมาจากรายงานล่าสุดของ Agent แต่ละเครื่อง" />
        @endforelse
    </x-data-table>
</x-layouts.app>
