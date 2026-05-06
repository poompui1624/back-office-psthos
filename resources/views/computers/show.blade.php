<x-layouts.app title="รายละเอียดคอมพิวเตอร์">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                {{ $computer->hostname }}
            </h1>

            <p class="text-sm text-gray-600">
                รายละเอียดเครื่องคอมพิวเตอร์และประวัติข้อมูลจาก Agent
            </p>
        </div>

        <div class="flex gap-2">
            @can('computer.update')
                <a href="{{ route('computers.edit', $computer) }}"
                   class="rounded bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600">
                    แก้ไข
                </a>
            @endcan

            <a href="{{ route('computers.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ย้อนกลับ
            </a>
        </div>
    </div>

    {{-- Summary --}}
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">สถานะ</div>
            <div class="mt-2 text-xl font-bold">
                {{ $computer->status }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">IP Address</div>
            <div class="mt-2 text-xl font-bold">
                {{ $computer->ip_address ?? '-' }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">RAM</div>
            <div class="mt-2 text-xl font-bold">
                {{ $computer->ram_gb ? $computer->ram_gb . ' GB' : '-' }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">Last Seen</div>
            <div class="mt-2 text-xl font-bold">
                {{ $computer->last_seen_at?->format('Y-m-d H:i') ?? '-' }}
            </div>
        </div>
    </div>

    {{-- Computer Info --}}
    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">
            ข้อมูลเครื่อง
        </h2>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <div class="text-sm text-gray-500">Hostname</div>
                <div class="font-medium">{{ $computer->hostname }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Machine UUID</div>
                <div class="font-medium">{{ $computer->machine_uuid ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">MAC Address</div>
                <div class="font-medium">{{ $computer->mac_address ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Serial Number</div>
                <div class="font-medium">{{ $computer->serial_number ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Manufacturer</div>
                <div class="font-medium">{{ $computer->manufacturer ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Model</div>
                <div class="font-medium">{{ $computer->model ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">OS</div>
                <div class="font-medium">
                    {{ $computer->os_name ?? '-' }}
                    {{ $computer->os_version ?? '' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">CPU</div>
                <div class="font-medium">{{ $computer->cpu_name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Storage</div>
                <div class="font-medium">
                    {{ $computer->storage_gb ? $computer->storage_gb . ' GB' : '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Source</div>
                <div class="font-medium">{{ $computer->source }}</div>
            </div>
        </div>
    </div>

    {{-- Ownership --}}
    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">
            หน่วยงาน / ผู้รับผิดชอบ
        </h2>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <div class="text-sm text-gray-500">พัสดุที่ผูกไว้</div>
                <div class="font-medium">
                    @if ($computer->asset)
                        {{ $computer->asset->asset_code }} - {{ $computer->asset->name }}
                    @else
                        -
                    @endif
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">หน่วยงาน</div>
                <div class="font-medium">
                    {{ $computer->department?->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ผู้รับผิดชอบ</div>
                <div class="font-medium">
                    {{ $computer->responsibleEmployee?->full_name ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Snapshots --}}
    <div class="rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">
            Snapshot History
        </h2>

        <p class="mb-4 text-sm text-gray-600">
            ประวัติข้อมูลที่ Agent ส่งเข้ามาแต่ละครั้ง
        </p>

        <div class="space-y-4">
            @forelse ($snapshots as $snapshot)
                <div class="rounded border border-gray-200 p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <div class="font-bold">
                                {{ $snapshot->reported_at?->format('Y-m-d H:i:s') ?? $snapshot->created_at->format('Y-m-d H:i:s') }}
                            </div>

                            <div class="text-sm text-gray-500">
                                Hostname: {{ $snapshot->hostname ?? '-' }}
                                |
                                IP: {{ $snapshot->ip_address ?? '-' }}
                            </div>
                        </div>

                        <div class="text-sm text-gray-500">
                            Snapshot #{{ $snapshot->id }}
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-4">
                        <div>
                            <div class="text-sm text-gray-500">OS</div>
                            <div class="font-medium">
                                {{ $snapshot->os_name ?? '-' }}
                                {{ $snapshot->os_version ?? '' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">CPU</div>
                            <div class="font-medium">
                                {{ $snapshot->cpu_name ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">RAM</div>
                            <div class="font-medium">
                                {{ $snapshot->ram_gb ? $snapshot->ram_gb . ' GB' : '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">Storage</div>
                            <div class="font-medium">
                                {{ $snapshot->storage_gb ? $snapshot->storage_gb . ' GB' : '-' }}
                            </div>
                        </div>
                    </div>

                    @if ($snapshot->installed_software)
                        <details class="mt-4">
                            <summary class="cursor-pointer font-medium text-gray-700">
                                ดู Software ที่ติดตั้ง
                                <span class="text-sm text-gray-500">
                                    ({{ count($snapshot->installed_software) }} รายการ)
                                </span>
                            </summary>

                            <div class="mt-3 max-h-80 overflow-auto rounded bg-gray-50 p-3">
                                <table class="w-full border-collapse text-sm">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border px-3 py-2 text-left">ชื่อโปรแกรม</th>
                                            <th class="border px-3 py-2 text-left">Version</th>
                                            <th class="border px-3 py-2 text-left">Publisher</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($snapshot->installed_software as $software)
                                            <tr>
                                                <td class="border px-3 py-2">
                                                    {{ $software['name'] ?? '-' }}
                                                </td>
                                                <td class="border px-3 py-2">
                                                    {{ $software['version'] ?? '-' }}
                                                </td>
                                                <td class="border px-3 py-2">
                                                    {{ $software['publisher'] ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </details>
                    @endif

                    @if ($snapshot->raw_payload)
                        <details class="mt-4">
                            <summary class="cursor-pointer font-medium text-gray-700">
                                ดู Raw Payload
                            </summary>

                            <pre class="mt-3 max-h-80 overflow-auto rounded bg-gray-900 p-3 text-xs text-white">{{ json_encode($snapshot->raw_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </details>
                    @endif
                </div>
            @empty
                <div class="rounded border border-dashed border-gray-300 p-8 text-center text-gray-500">
                    ยังไม่มี Snapshot จาก Agent
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $snapshots->links() }}
        </div>
    </div>
</x-layouts.app>
