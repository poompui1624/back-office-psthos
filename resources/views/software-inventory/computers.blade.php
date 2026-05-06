<x-layouts.app title="เครื่องที่ติดตั้ง Software">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">เครื่องที่ติดตั้ง Software</h1>
            <p class="text-sm text-gray-600">
                {{ $name }}
                @if ($version)
                    / Version: {{ $version }}
                @endif
                @if ($publisher)
                    / Publisher: {{ $publisher }}
                @endif
            </p>
        </div>

        <a href="{{ route('software-inventory.index') }}"
           class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
            ย้อนกลับ
        </a>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">Hostname</th>
                    <th class="border px-4 py-2 text-left">IP Address</th>
                    <th class="border px-4 py-2 text-left">OS</th>
                    <th class="border px-4 py-2 text-left">หน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">ผู้รับผิดชอบ</th>
                    <th class="border px-4 py-2 text-left">Last Seen</th>
                    <th class="border px-4 py-2 text-center">รายละเอียด</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($computers as $computer)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $computer->hostname }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $computer->ip_address ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $computer->os_name ?? '-' }}
                            {{ $computer->os_version ?? '' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $computer->department?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $computer->responsibleEmployee?->full_name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $computer->last_seen_at?->format('Y-m-d H:i') ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <a href="{{ route('computers.show', $computer) }}"
                               class="rounded bg-gray-800 px-3 py-1 text-sm text-white">
                                เปิด
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบเครื่องที่ติดตั้ง Software นี้
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
