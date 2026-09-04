<x-layouts.app title="Computer Agents">
    <x-page-header title="Computer Agents"
                   subtitle="จัดการ Token สำหรับโปรแกรม Agent ที่ส่งข้อมูลเครื่อง Client เข้า Server">
        @can('computer.agent_manage')
            <x-btn :href="route('computer-agents.create')" icon="shield">สร้าง Agent Token</x-btn>
        @endcan
    </x-page-header>

    @if (session('plain_token'))
        <x-alert type="warning" title="Token ใหม่" class="mb-4">
            <p>ให้คัดลอก Token นี้เก็บไว้ทันที ระบบจะแสดงเพียงครั้งเดียว</p>

            <div class="mt-3 overflow-x-auto rounded-xl bg-slate-900 p-3 font-mono text-sm text-white">
                {{ session('plain_token') }}
            </div>
        </x-alert>
    @endif

    <x-filter-bar :action="route('computer-agents.index')">
        <x-form.field label="ค้นหา" class="flex-1">
            <x-form.input name="search" :value="$search" placeholder="ชื่อ Agent / IP ล่าสุด" />
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>ชื่อ Agent</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th>Last Seen</x-data-table.th>
            <x-data-table.th>IP ล่าสุด</x-data-table.th>
            <x-data-table.th>User Agent</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($agents as $agent)
            <x-data-table.row>
                <x-data-table.td class="font-medium text-slate-900">{{ $agent->name }}</x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$agent->is_active ? 'success' : 'slate'" dot>
                        {{ $agent->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td class="tabular-nums">
                    {{ $agent->last_seen_at?->format('Y-m-d H:i:s') ?? '-' }}
                </x-data-table.td>

                <x-data-table.td>{{ $agent->last_ip_address ?? '-' }}</x-data-table.td>

                <x-data-table.td>
                    <div class="max-w-xs truncate text-xs text-slate-500">{{ $agent->last_user_agent ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-btn :href="route('computer-agents.edit', $agent)" variant="secondary" size="sm">แก้ไข</x-btn>

                        <form method="POST"
                              action="{{ route('computer-agents.regenerate-token', $agent) }}"
                              onsubmit="return confirm('ยืนยันการสร้าง Token ใหม่? Token เดิมจะใช้งานไม่ได้ทันที')">
                            @csrf

                            <x-btn type="submit" variant="warning" size="sm">Regenerate</x-btn>
                        </form>

                        <form method="POST"
                              action="{{ route('computer-agents.destroy', $agent) }}"
                              onsubmit="return confirm('ยืนยันการลบ Agent นี้?')">
                            @csrf
                            @method('DELETE')

                            <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                        </form>
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="6" icon="shield" title="ไม่พบข้อมูล Agent"
                                description="สร้าง Agent Token เพื่อให้เครื่อง Client ส่งข้อมูลเข้าระบบ">
                @can('computer.agent_manage')
                    <x-btn :href="route('computer-agents.create')">สร้าง Agent Token</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $agents->links() }}</div>
</x-layouts.app>
