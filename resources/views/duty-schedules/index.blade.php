<x-layouts.app title="ตารางเวร">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ตารางเวร</h1>
            <p class="text-sm text-gray-600">จัดตารางเวรของบุคลากรแต่ละหน่วยงาน</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('duty-schedules.calendar') }}"
            class="rounded bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">
                ปฏิทินเวร
            </a>
            @can('duty.create')
                <a href="{{ route('duty-schedules.bulk-create') }}"
                class="rounded bg-purple-600 px-4 py-2 text-white hover:bg-purple-700">
                    สร้างหลายรายการ
                </a>

                <a href="{{ route('duty-schedules.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    เพิ่มตารางเวร
                </a>
            @endcan

            <a href="{{ route('shift-types.index') }}"
               class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ประเภทเวร
            </a>
        </div>
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
        <form method="GET" action="{{ route('duty-schedules.index') }}" class="space-y-3">
            <div class="grid gap-3 md:grid-cols-6">
                <input type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="ค้นหารหัส / ชื่อบุคลากร"
                    class="rounded border-gray-300 md:col-span-2">

                <input type="date"
                    name="date_from"
                    value="{{ $dateFrom }}"
                    class="rounded border-gray-300">

                <input type="date"
                    name="date_to"
                    value="{{ $dateTo }}"
                    class="rounded border-gray-300">

                <select name="department_id" class="rounded border-gray-300">
                    <option value="">ทุกหน่วยงาน</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected($departmentId == $department->id)>
                            {{ $department->code }} - {{ $department->name }}
                        </option>
                    @endforeach
                </select>

                <select name="per_page" class="rounded border-gray-300">
                    <option value="10" @selected($perPage == 10)>10 รายการ/หน้า</option>
                    <option value="25" @selected($perPage == 25)>25 รายการ/หน้า</option>
                    <option value="50" @selected($perPage == 50)>50 รายการ/หน้า</option>
                    <option value="100" @selected($perPage == 100)>100 รายการ/หน้า</option>
                </select>
            </div>

            <div class="grid gap-3 md:grid-cols-6">
                <input type="text"
                    name="role_group"
                    value="{{ $roleGroup }}"
                    placeholder="กรองกลุ่มงาน เช่น พยาบาล"
                    class="rounded border-gray-300 md:col-span-2">

                <div class="flex gap-2 md:col-span-4">
                    <button type="submit"
                            class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                        ค้นหา
                    </button>

                    <a href="{{ route('duty-schedules.index') }}"
                    class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                        ล้าง
                    </a>
                </div>
            </div>
        </form>
    </div>

    @php
        $statusLabels = [
            'assigned' => 'มอบหมายแล้ว',
            'confirmed' => 'ยืนยันแล้ว',
            'cancelled' => 'ยกเลิก',
        ];

        $statusClasses = [
            'assigned' => 'bg-blue-100 text-blue-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
        ];
    @endphp

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">วันที่</th>
                    <th class="border px-4 py-2 text-left">บุคลากร</th>
                    <th class="border px-4 py-2 text-left">หน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">เวร</th>
                    <th class="border px-4 py-2 text-center">เวลา</th>
                    <th class="border px-4 py-2 text-left">กลุ่มงาน</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-left">ผู้มอบหมาย</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($schedules as $schedule)
                    <tr>
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ $schedule->work_date?->format('Y-m-d') }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $schedule->employee?->employee_code }}
                            <div class="text-xs text-gray-500">
                                {{ $schedule->employee?->full_name ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $schedule->department?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $schedule->shiftType?->name ?? '-' }}
                            <div class="text-xs text-gray-500">
                                {{ $schedule->shiftType?->code ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2 text-center whitespace-nowrap">
                            {{ $schedule->start_at?->format('H:i') }}
                            -
                            {{ $schedule->end_at?->format('H:i') }}

                            @if ($schedule->start_at && $schedule->end_at && $schedule->start_at->format('Y-m-d') !== $schedule->end_at->format('Y-m-d'))
                                <div class="text-xs text-purple-600">
                                    ข้ามวัน
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $schedule->role_group ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <span class="rounded px-2 py-1 text-xs {{ $statusClasses[$schedule->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$schedule->status] ?? $schedule->status }}
                            </span>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $schedule->assignedBy?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('duty.update')
                                    <a href="{{ route('duty-schedules.edit', $schedule) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('duty.delete')
                                    <form method="POST"
                                          action="{{ route('duty-schedules.destroy', $schedule) }}"
                                          onsubmit="return confirm('ยืนยันการลบตารางเวรนี้?')">
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
                        <td colspan="9" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบข้อมูลตารางเวร
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 rounded bg-white p-4 shadow">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="text-sm text-gray-600">
                @if ($schedules->total() > 0)
                    แสดงรายการที่
                    <span class="font-semibold">{{ $schedules->firstItem() }}</span>
                    ถึง
                    <span class="font-semibold">{{ $schedules->lastItem() }}</span>
                    จากทั้งหมด
                    <span class="font-semibold">{{ $schedules->total() }}</span>
                    รายการ
                @else
                    ไม่มีข้อมูล
                @endif
            </div>

            @if ($schedules->lastPage() > 1)
                <div class="flex flex-wrap items-center gap-2">
                    @if ($schedules->onFirstPage())
                        <span class="rounded border border-gray-200 bg-gray-100 px-3 py-1 text-sm text-gray-400">
                            ก่อนหน้า
                        </span>
                    @else
                        <a href="{{ $schedules->previousPageUrl() }}"
                        class="rounded border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-100">
                            ก่อนหน้า
                        </a>
                    @endif

                    @for ($page = 1; $page <= $schedules->lastPage(); $page++)
                        @if ($page === $schedules->currentPage())
                            <span class="rounded bg-gray-800 px-3 py-1 text-sm text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $schedules->url($page) }}"
                            class="rounded border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-100">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if ($schedules->hasMorePages())
                        <a href="{{ $schedules->nextPageUrl() }}"
                        class="rounded border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-100">
                            ถัดไป
                        </a>
                    @else
                        <span class="rounded border border-gray-200 bg-gray-100 px-3 py-1 text-sm text-gray-400">
                            ถัดไป
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
