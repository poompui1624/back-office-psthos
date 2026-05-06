<x-layouts.app title="ประเภทเวร">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ประเภทเวร</h1>
            <p class="text-sm text-gray-600">จัดการประเภทเวรและช่วงเวลาทำงาน</p>
        </div>

        @can('duty.create')
            <a href="{{ route('shift-types.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่มประเภทเวร
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
        <form method="GET" action="{{ route('shift-types.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัสเวร / ชื่อเวร"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('shift-types.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ล้าง
            </a>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">รหัส</th>
                    <th class="border px-4 py-2 text-left">ชื่อเวร</th>
                    <th class="border px-4 py-2 text-center">เวลา</th>
                    <th class="border px-4 py-2 text-center">ข้ามวัน</th>
                    <th class="border px-4 py-2 text-center">สี</th>
                    <th class="border px-4 py-2 text-center">จำนวนตารางเวร</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($shiftTypes as $shiftType)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $shiftType->code }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $shiftType->name }}

                            @if ($shiftType->description)
                                <div class="text-xs text-gray-500">
                                    {{ $shiftType->description }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center whitespace-nowrap">
                            {{ substr($shiftType->start_time, 0, 5) }}
                            -
                            {{ substr($shiftType->end_time, 0, 5) }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($shiftType->crosses_midnight)
                                <span class="rounded bg-purple-100 px-2 py-1 text-xs text-purple-800">
                                    ใช่
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                    ไม่ใช่
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $shiftType->color ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $shiftType->duty_schedules_count }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($shiftType->is_active)
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                    ใช้งาน
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                    ปิดใช้งาน
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('duty.update')
                                    <a href="{{ route('shift-types.edit', $shiftType) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('duty.delete')
                                    <form method="POST"
                                          action="{{ route('shift-types.destroy', $shiftType) }}"
                                          onsubmit="return confirm('ยืนยันการลบประเภทเวรนี้?')">
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
                            ไม่พบข้อมูลประเภทเวร
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $shiftTypes->links() }}
    </div>
</x-layouts.app>
