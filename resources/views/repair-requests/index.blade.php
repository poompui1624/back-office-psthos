<x-layouts.app title="ระบบแจ้งซ่อม">
    @php
        $repairStatuses = ['new' => 'ใหม่', 'in_progress' => 'กำลังดำเนินการ', 'completed' => 'เสร็จแล้ว', 'cancelled' => 'ยกเลิก'];
        $repairTones = ['new' => 'info', 'in_progress' => 'warning', 'completed' => 'success', 'cancelled' => 'slate'];

        $priorities = ['urgent' => 'ด่วนมาก', 'high' => 'สูง', 'normal' => 'ปกติ', 'low' => 'ต่ำ'];
        $priorityTones = ['urgent' => 'danger', 'high' => 'warning', 'normal' => 'info', 'low' => 'slate'];
    @endphp

    <x-page-header title="ระบบแจ้งซ่อม" subtitle="ติดตามรายการแจ้งซ่อมทั้งหมด">
        <x-btn :href="route('repair-requests.kanban')" variant="secondary" icon="clipboard">Kanban</x-btn>

        @can('repair.create')
            <x-btn :href="route('repair-requests.create')" icon="wrench">แจ้งซ่อมใหม่</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('repair-requests.index')">
        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="เลขงาน / หัวข้อ / ผู้แจ้ง / หน่วยงาน" />
        </x-form.field>

        <x-form.field label="สถานะ">
            <x-form.select name="status" class="w-48">
                <option value="">ทุกสถานะ</option>

                @foreach ($repairStatuses as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>เลขงาน</x-data-table.th>
            <x-data-table.th>หัวข้อ</x-data-table.th>
            <x-data-table.th>ผู้แจ้ง / หน่วยงาน</x-data-table.th>
            <x-data-table.th>ประเภท</x-data-table.th>
            <x-data-table.th align="center">ความเร่งด่วน</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th>ผู้รับผิดชอบ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($repairRequests as $repairRequest)
            <x-data-table.row>
                <x-data-table.td class="whitespace-nowrap">
                    <div class="font-medium text-slate-900">{{ $repairRequest->ticket_no }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $repairRequest->created_at->format('Y-m-d H:i') }}</div>
                </x-data-table.td>

                <x-data-table.td>
                    <div class="font-medium text-slate-900">{{ $repairRequest->title }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $repairRequest->location ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $repairRequest->requesterEmployee?->full_name ?? $repairRequest->requester?->name ?? '-' }}</div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $repairRequest->department?->name ?? '-' }}</div>
                </x-data-table.td>

                <x-data-table.td>
                    <div>{{ $repairRequest->category }}</div>

                    @if ($repairRequest->repairable)
                        <div class="mt-0.5 text-xs text-slate-500">{{ class_basename($repairRequest->repairable_type) }}</div>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$priorityTones[$repairRequest->priority] ?? 'info'">
                        {{ $priorities[$repairRequest->priority] ?? 'ปกติ' }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td align="center">
                    <x-badge :tone="$repairTones[$repairRequest->status] ?? 'slate'" dot>
                        {{ $repairStatuses[$repairRequest->status] ?? $repairRequest->status }}
                    </x-badge>
                </x-data-table.td>

                <x-data-table.td>{{ $repairRequest->assignedUser?->name ?? '-' }}</x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        <x-btn :href="route('repair-requests.show', $repairRequest)" variant="secondary" size="sm">
                            รายละเอียด
                        </x-btn>

                        @can('repair.update')
                            <x-btn :href="route('repair-requests.edit', $repairRequest)" variant="secondary" size="sm">แก้ไข</x-btn>
                        @endcan

                        @can('repair.delete')
                            <form method="POST"
                                  action="{{ route('repair-requests.destroy', $repairRequest) }}"
                                  onsubmit="return confirm('ยืนยันการลบรายการแจ้งซ่อมนี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="8" icon="wrench" title="ไม่พบรายการแจ้งซ่อม"
                                description="ลองเปลี่ยนคำค้นหาหรือสถานะ">
                @can('repair.create')
                    <x-btn :href="route('repair-requests.create')">แจ้งซ่อมใหม่</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $repairRequests->links() }}</div>
</x-layouts.app>
