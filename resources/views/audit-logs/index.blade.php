<x-layouts.app title="Audit Log">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Audit Log</h1>
        <p class="text-sm text-gray-600">ประวัติการเพิ่ม แก้ไข และลบข้อมูลในระบบ</p>
    </div>

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="grid gap-3 md:grid-cols-4">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหา module / user / IP"
                   class="rounded border-gray-300 md:col-span-2">

            <select name="action" class="rounded border-gray-300">
                <option value="">ทุก Action</option>
                <option value="created" @selected($action === 'created')>Created</option>
                <option value="updated" @selected($action === 'updated')>Updated</option>
                <option value="deleted" @selected($action === 'deleted')>Deleted</option>
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                    ค้นหา
                </button>

                <a href="{{ route('audit-logs.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                    ล้าง
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">วันที่</th>
                    <th class="border px-4 py-2 text-left">ผู้ใช้</th>
                    <th class="border px-4 py-2 text-left">Action</th>
                    <th class="border px-4 py-2 text-left">Module</th>
                    <th class="border px-4 py-2 text-left">Record</th>
                    <th class="border px-4 py-2 text-left">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $log->user?->name ?? 'system' }}
                            <div class="text-xs text-gray-500">
                                {{ $log->user?->email }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            @if ($log->action === 'created')
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">created</span>
                            @elseif ($log->action === 'updated')
                                <span class="rounded bg-yellow-100 px-2 py-1 text-xs text-yellow-800">updated</span>
                            @elseif ($log->action === 'deleted')
                                <span class="rounded bg-red-100 px-2 py-1 text-xs text-red-800">deleted</span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800">{{ $log->action }}</span>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $log->module }}
                        </td>

                        <td class="border px-4 py-2">
                            #{{ $log->auditable_id }}
                            <div class="text-xs text-gray-500">
                                {{ class_basename($log->auditable_type) }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>

                    @if ($log->old_values || $log->new_values)
                        <tr>
                            <td colspan="6" class="border bg-gray-50 px-4 py-3">
                                <details>
                                    <summary class="cursor-pointer text-sm font-medium text-gray-700">
                                        ดูรายละเอียดการเปลี่ยนแปลง
                                    </summary>

                                    <div class="mt-3 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <div class="mb-1 text-sm font-semibold">Old Values</div>
                                            <pre class="overflow-auto rounded bg-gray-900 p-3 text-xs text-white">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>

                                        <div>
                                            <div class="mb-1 text-sm font-semibold">New Values</div>
                                            <pre class="overflow-auto rounded bg-gray-900 p-3 text-xs text-white">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบข้อมูล Audit Log
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</x-layouts.app>
