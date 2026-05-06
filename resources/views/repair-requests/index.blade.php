<x-layouts.app title="ระบบแจ้งซ่อม">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ระบบแจ้งซ่อม</h1>
            <p class="text-sm text-gray-600">ติดตามรายการแจ้งซ่อมทั้งหมด</p>
        </div>

        <a href="{{ route('repair-requests.kanban') }}"
        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
            Kanban Board
        </a>

        @can('repair.create')
            <a href="{{ route('repair-requests.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                แจ้งซ่อมใหม่
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('repair-requests.index') }}" class="grid gap-3 md:grid-cols-4">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหาเลขงาน / หัวข้อ / ผู้แจ้ง / หน่วยงาน"
                   class="rounded border-gray-300 md:col-span-2">

            <select name="status" class="rounded border-gray-300">
                <option value="">ทุกสถานะ</option>
                <option value="new" @selected($status === 'new')>ใหม่</option>
                <option value="in_progress" @selected($status === 'in_progress')>กำลังดำเนินการ</option>
                <option value="completed" @selected($status === 'completed')>เสร็จแล้ว</option>
                <option value="cancelled" @selected($status === 'cancelled')>ยกเลิก</option>
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                    ค้นหา
                </button>

                <a href="{{ route('repair-requests.index') }}"
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
                    <th class="border px-4 py-2 text-left">เลขงาน</th>
                    <th class="border px-4 py-2 text-left">หัวข้อ</th>
                    <th class="border px-4 py-2 text-left">ผู้แจ้ง / หน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">ประเภท</th>
                    <th class="border px-4 py-2 text-center">ความเร่งด่วน</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-left">ผู้รับผิดชอบ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($repairRequests as $repairRequest)
                    <tr>
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ $repairRequest->ticket_no }}
                            <div class="text-xs text-gray-500">
                                {{ $repairRequest->created_at->format('Y-m-d H:i') }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            <div class="font-medium">{{ $repairRequest->title }}</div>
                            <div class="text-xs text-gray-500">{{ $repairRequest->location ?? '-' }}</div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $repairRequest->requesterEmployee?->full_name ?? $repairRequest->requester?->name ?? '-' }}
                            <div class="text-xs text-gray-500">
                                {{ $repairRequest->department?->name ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $repairRequest->category }}

                            @if ($repairRequest->repairable)
                                <div class="text-xs text-gray-500">
                                    {{ class_basename($repairRequest->repairable_type) }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($repairRequest->priority === 'urgent')
                                <span class="rounded bg-red-100 px-2 py-1 text-xs text-red-800">ด่วนมาก</span>
                            @elseif ($repairRequest->priority === 'high')
                                <span class="rounded bg-orange-100 px-2 py-1 text-xs text-orange-800">สูง</span>
                            @elseif ($repairRequest->priority === 'low')
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">ต่ำ</span>
                            @else
                                <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">ปกติ</span>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($repairRequest->status === 'new')
                                <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">ใหม่</span>
                            @elseif ($repairRequest->status === 'in_progress')
                                <span class="rounded bg-yellow-100 px-2 py-1 text-xs text-yellow-800">กำลังดำเนินการ</span>
                            @elseif ($repairRequest->status === 'completed')
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">เสร็จแล้ว</span>
                            @elseif ($repairRequest->status === 'cancelled')
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">ยกเลิก</span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">{{ $repairRequest->status }}</span>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $repairRequest->assignedUser?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex flex-wrap justify-center gap-2">
                                <a href="{{ route('repair-requests.show', $repairRequest) }}"
                                   class="rounded bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-900">
                                    รายละเอียด
                                </a>

                                @can('repair.update')
                                    <a href="{{ route('repair-requests.edit', $repairRequest) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('repair.delete')
                                    <form method="POST"
                                          action="{{ route('repair-requests.destroy', $repairRequest) }}"
                                          onsubmit="return confirm('ยืนยันการลบรายการแจ้งซ่อมนี้?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="rounded bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700">
                                            ลบ
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบรายการแจ้งซ่อม
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $repairRequests->links() }}
    </div>
</x-layouts.app>
