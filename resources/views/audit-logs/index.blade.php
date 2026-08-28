<x-layouts.app title="Audit Log">
    @php
        $actionTones = ['created' => 'success', 'updated' => 'warning', 'deleted' => 'danger'];
    @endphp

    <x-page-header title="Audit Log" subtitle="ประวัติการเพิ่ม แก้ไข และลบข้อมูลในระบบ" />

    <x-filter-bar :action="route('audit-logs.index')">
        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="module / user / IP" />
        </x-form.field>

        <x-form.field label="Action">
            <x-form.select name="action" class="w-44">
                <option value="">ทุก Action</option>

                @foreach (['created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted'] as $value => $label)
                    <option value="{{ $value }}" @selected($action === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>วันที่</x-data-table.th>
            <x-data-table.th>ผู้ใช้</x-data-table.th>
            <x-data-table.th>Action</x-data-table.th>
            <x-data-table.th>Module</x-data-table.th>
            <x-data-table.th>Record</x-data-table.th>
            <x-data-table.th>IP</x-data-table.th>
        </x-slot:head>

        @forelse ($logs as $log)
            <x-data-table.row>
                <x-data-table.td class="whitespace-nowrap tabular-nums">
                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $log->user?->name ?? 'system' }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $log->user?->email }}</div>
                </x-data-table.td>

                <x-data-table.td>
                    <x-badge :tone="$actionTones[$log->action] ?? 'slate'">{{ $log->action }}</x-badge>
                </x-data-table.td>

                <x-data-table.td>{{ $log->module }}</x-data-table.td>

                <x-data-table.td>
                    <div>#{{ $log->auditable_id }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ class_basename($log->auditable_type) }}</div>
                </x-data-table.td>

                <x-data-table.td>{{ $log->ip_address ?? '-' }}</x-data-table.td>
            </x-data-table.row>

            @if ($log->old_values || $log->new_values)
                <tr class="bg-slate-50/70">
                    <td colspan="6" class="px-4 py-3">
                        <details class="group">
                            <summary class="flex cursor-pointer items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900">
                                <x-icon name="chevron-right" class="h-3.5 w-3.5 transition group-open:rotate-90" />
                                ดูรายละเอียดการเปลี่ยนแปลง
                            </summary>

                            <div class="mt-3 grid gap-4 md:grid-cols-2">
                                <div>
                                    <div class="mb-1.5 text-sm font-semibold text-slate-700">Old Values</div>
                                    <pre class="overflow-auto rounded-xl bg-slate-900 p-3 text-xs text-white">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>

                                <div>
                                    <div class="mb-1.5 text-sm font-semibold text-slate-700">New Values</div>
                                    <pre class="overflow-auto rounded-xl bg-slate-900 p-3 text-xs text-white">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        </details>
                    </td>
                </tr>
            @endif
        @empty
            <x-data-table.empty :colspan="6" icon="document" title="ไม่พบประวัติการใช้งาน"
                                description="ลองเปลี่ยนคำค้นหาหรือ Action" />
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $logs->links() }}</div>
</x-layouts.app>
