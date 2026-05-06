<x-layouts.app title="ระบบการลา">
    <div class="flex gap-2">
        <a href="{{ route('leave-requests.dashboard') }}"
        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
            Dashboard
        </a>

        <a href="{{ route('leave-requests.calendar') }}"
        class="rounded bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">
            ปฏิทินการลา
        </a>

        @can('leave.create')
            <a href="{{ route('leave-requests.create') }}"
            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                ยื่นคำขอลา
            </a>
        @endcan
    </div>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ระบบการลา</h1>
            <p class="text-sm text-gray-600">รายการคำขอลาของบุคลากร</p>
        </div>

        @can('leave.create')
            <a href="{{ route('leave-requests.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                ยื่นคำขอลา
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('leave-requests.index') }}" class="grid gap-3 md:grid-cols-4">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหาเลขคำขอ / ชื่อบุคลากร / ประเภทลา"
                   class="rounded border-gray-300 md:col-span-2">

            <select name="status" class="rounded border-gray-300">
                <option value="">ทุกสถานะ</option>
                <option value="pending" @selected($status === 'pending')>รออนุมัติ</option>
                <option value="approved" @selected($status === 'approved')>อนุมัติแล้ว</option>
                <option value="rejected" @selected($status === 'rejected')>ไม่อนุมัติ</option>
                <option value="cancelled" @selected($status === 'cancelled')>ยกเลิก</option>
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                    ค้นหา
                </button>

                <a href="{{ route('leave-requests.index') }}"
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
                    <th class="border px-4 py-2 text-left">เลขคำขอ</th>
                    <th class="border px-4 py-2 text-left">บุคลากร</th>
                    <th class="border px-4 py-2 text-left">ประเภทการลา</th>
                    <th class="border px-4 py-2 text-left">ช่วงวันที่</th>
                    <th class="border px-4 py-2 text-center">จำนวนวัน</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-left">ผู้อนุมัติ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($leaveRequests as $leaveRequest)
                    <tr>
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ $leaveRequest->request_no }}
                            <div class="text-xs text-gray-500">
                                {{ $leaveRequest->created_at->format('Y-m-d H:i') }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $leaveRequest->employee?->full_name ?? '-' }}
                            <div class="text-xs text-gray-500">
                                {{ $leaveRequest->department?->name ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $leaveRequest->leaveType?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $leaveRequest->start_date?->format('Y-m-d') }}
                            ถึง
                            {{ $leaveRequest->end_date?->format('Y-m-d') }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $leaveRequest->total_days }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($leaveRequest->status === 'pending')
                                <span class="rounded bg-yellow-100 px-2 py-1 text-xs text-yellow-800">
                                    รออนุมัติ
                                </span>
                            @elseif ($leaveRequest->status === 'approved')
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                    อนุมัติแล้ว
                                </span>
                            @elseif ($leaveRequest->status === 'rejected')
                                <span class="rounded bg-red-100 px-2 py-1 text-xs text-red-800">
                                    ไม่อนุมัติ
                                </span>
                            @elseif ($leaveRequest->status === 'cancelled')
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800">
                                    ยกเลิก
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800">
                                    {{ $leaveRequest->status }}
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $leaveRequest->approver?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex flex-wrap justify-center gap-2">
                                <a href="{{ route('leave-requests.show', $leaveRequest) }}"
                                   class="rounded bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-900">
                                    รายละเอียด
                                </a>

                                @if ($leaveRequest->isPending())
                                    @can('leave.update')
                                        <a href="{{ route('leave-requests.edit', $leaveRequest) }}"
                                           class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                            แก้ไข
                                        </a>
                                    @endcan

                                    @can('leave.delete')
                                        <form method="POST"
                                              action="{{ route('leave-requests.destroy', $leaveRequest) }}"
                                              onsubmit="return confirm('ยืนยันการลบคำขอนี้?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="rounded bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700">
                                                ลบ
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบคำขอลา
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $leaveRequests->links() }}
    </div>
</x-layouts.app>
