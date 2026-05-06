<x-layouts.app title="Leave Dashboard">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Leave Dashboard</h1>
            <p class="text-sm text-gray-600">ภาพรวมระบบการลา</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('leave-requests.calendar') }}"
               class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ปฏิทินการลา
            </a>

            @can('leave.create')
                <a href="{{ route('leave-requests.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    ยื่นคำขอลา
                </a>
            @endcan
        </div>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-3 lg:grid-cols-6">
        <a href="{{ route('leave-requests.index', ['status' => 'pending']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">รออนุมัติ</div>
            <div class="mt-2 text-3xl font-bold text-yellow-600">
                {{ $summary['pending'] }}
            </div>
        </a>

        <a href="{{ route('leave-requests.index', ['status' => 'approved']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">อนุมัติแล้ว</div>
            <div class="mt-2 text-3xl font-bold text-green-600">
                {{ $summary['approved'] }}
            </div>
        </a>

        <a href="{{ route('leave-requests.index', ['status' => 'rejected']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">ไม่อนุมัติ</div>
            <div class="mt-2 text-3xl font-bold text-red-600">
                {{ $summary['rejected'] }}
            </div>
        </a>

        <a href="{{ route('leave-requests.index', ['status' => 'cancelled']) }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">ยกเลิก</div>
            <div class="mt-2 text-3xl font-bold text-gray-600">
                {{ $summary['cancelled'] }}
            </div>
        </a>

        <a href="{{ route('leave-requests.calendar') }}"
           class="rounded bg-white p-5 shadow hover:shadow-md">
            <div class="text-sm text-gray-500">รายการเดือนนี้</div>
            <div class="mt-2 text-3xl font-bold text-blue-600">
                {{ $summary['this_month'] }}
            </div>
        </a>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">วันนี้มีคนลา</div>
            <div class="mt-2 text-3xl font-bold text-purple-600">
                {{ $summary['today_on_leave'] }}
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded bg-white p-6 shadow">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold">ลาวันนี้</h2>
                <span class="rounded bg-purple-100 px-2 py-1 text-xs text-purple-800">
                    {{ $todayLeaves->count() }} รายการ
                </span>
            </div>

            <div class="space-y-3">
                @forelse ($todayLeaves as $leave)
                    <a href="{{ route('leave-requests.show', $leave) }}"
                       class="block rounded border border-gray-200 p-3 hover:bg-gray-50">
                        <div class="font-medium">
                            {{ $leave->employee?->full_name ?? '-' }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $leave->leaveType?->name ?? '-' }}
                            |
                            {{ $leave->department?->name ?? '-' }}
                        </div>

                        <div class="mt-1 text-xs text-gray-400">
                            {{ $leave->start_date?->format('Y-m-d') }}
                            ถึง
                            {{ $leave->end_date?->format('Y-m-d') }}
                        </div>
                    </a>
                @empty
                    <div class="rounded border border-dashed border-gray-300 p-6 text-center text-gray-500">
                        วันนี้ไม่มีรายการลา
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded bg-white p-6 shadow">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold">รออนุมัติล่าสุด</h2>

                <a href="{{ route('leave-requests.index', ['status' => 'pending']) }}"
                   class="text-sm text-blue-600 hover:underline">
                    ดูทั้งหมด
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($pendingRequests as $leave)
                    <a href="{{ route('leave-requests.show', $leave) }}"
                       class="block rounded border border-gray-200 p-3 hover:bg-gray-50">
                        <div class="font-medium">
                            {{ $leave->request_no }}
                        </div>

                        <div class="text-sm text-gray-700">
                            {{ $leave->employee?->full_name ?? '-' }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $leave->leaveType?->name ?? '-' }}
                            /
                            {{ $leave->total_days }} วัน
                        </div>

                        <div class="mt-1 text-xs text-gray-400">
                            {{ $leave->created_at->format('Y-m-d H:i') }}
                        </div>
                    </a>
                @empty
                    <div class="rounded border border-dashed border-gray-300 p-6 text-center text-gray-500">
                        ไม่มีคำขอรออนุมัติ
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded bg-white p-6 shadow">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold">รายการลาถัดไป</h2>

                <a href="{{ route('leave-requests.calendar') }}"
                   class="text-sm text-blue-600 hover:underline">
                    ดูปฏิทิน
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($upcomingLeaves as $leave)
                    <a href="{{ route('leave-requests.show', $leave) }}"
                       class="block rounded border border-gray-200 p-3 hover:bg-gray-50">
                        <div class="font-medium">
                            {{ $leave->employee?->full_name ?? '-' }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $leave->leaveType?->name ?? '-' }}
                            |
                            {{ $leave->department?->name ?? '-' }}
                        </div>

                        <div class="mt-1 text-xs text-gray-400">
                            {{ $leave->start_date?->format('Y-m-d') }}
                            ถึง
                            {{ $leave->end_date?->format('Y-m-d') }}
                        </div>
                    </a>
                @empty
                    <div class="rounded border border-dashed border-gray-300 p-6 text-center text-gray-500">
                        ไม่มีรายการลาถัดไป
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
