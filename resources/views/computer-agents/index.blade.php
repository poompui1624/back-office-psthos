<x-layouts.app title="Computer Agents">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Computer Agents</h1>
            <p class="text-sm text-gray-600">
                จัดการ Token สำหรับโปรแกรม Agent ที่ส่งข้อมูลเครื่อง Client เข้า Server
            </p>
        </div>

        @can('computer.agent_manage')
            <a href="{{ route('computer-agents.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                สร้าง Agent Token
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('plain_token'))
        <div class="mb-4 rounded border border-yellow-300 bg-yellow-50 p-4 text-yellow-900">
            <div class="font-bold">
                Token ใหม่
            </div>

            <p class="mt-1 text-sm">
                ให้คัดลอก Token นี้เก็บไว้ทันที ระบบจะแสดงเพียงครั้งเดียว
            </p>

            <div class="mt-3 rounded bg-gray-900 p-3 font-mono text-sm text-white">
                {{ session('plain_token') }}
            </div>
        </div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('computer-agents.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหาชื่อ Agent / IP ล่าสุด"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('computer-agents.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ล้าง
            </a>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">ชื่อ Agent</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-left">Last Seen</th>
                    <th class="border px-4 py-2 text-left">IP ล่าสุด</th>
                    <th class="border px-4 py-2 text-left">User Agent</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($agents as $agent)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $agent->name }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($agent->is_active)
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                    เปิดใช้งาน
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                    ปิดใช้งาน
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $agent->last_seen_at?->format('Y-m-d H:i:s') ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $agent->last_ip_address ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            <div class="max-w-xs truncate text-xs text-gray-500">
                                {{ $agent->last_user_agent ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex flex-wrap justify-center gap-2">
                                <a href="{{ route('computer-agents.edit', $agent) }}"
                                   class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                    แก้ไข
                                </a>

                                <form method="POST"
                                      action="{{ route('computer-agents.regenerate-token', $agent) }}"
                                      onsubmit="return confirm('ยืนยันการสร้าง Token ใหม่? Token เดิมจะใช้งานไม่ได้ทันที')">
                                    @csrf

                                    <button type="submit"
                                            class="rounded bg-purple-600 px-3 py-1 text-sm text-white hover:bg-purple-700">
                                        Regenerate
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('computer-agents.destroy', $agent) }}"
                                      onsubmit="return confirm('ยืนยันการลบ Agent นี้?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="rounded bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700">
                                        ลบ
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบข้อมูล Agent
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $agents->links() }}
    </div>
</x-layouts.app>
