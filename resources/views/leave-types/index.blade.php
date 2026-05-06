<x-layouts.app title="ประเภทการลา">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ประเภทการลา</h1>
            <p class="text-sm text-gray-600">จัดการประเภทการลาของบุคลากร</p>
        </div>

        @can('leave.create')
            <a href="{{ route('leave-types.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่มประเภทการลา
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
        <form method="GET" action="{{ route('leave-types.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัส / ชื่อประเภทการลา"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('leave-types.index') }}"
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
                    <th class="border px-4 py-2 text-left">ชื่อประเภท</th>
                    <th class="border px-4 py-2 text-center">วัน/ปี</th>
                    <th class="border px-4 py-2 text-center">ต้องแนบเอกสาร</th>
                    <th class="border px-4 py-2 text-center">จำนวนคำขอ</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($leaveTypes as $leaveType)
                    <tr>
                        <td class="border px-4 py-2">{{ $leaveType->code }}</td>

                        <td class="border px-4 py-2">
                            {{ $leaveType->name }}

                            @if ($leaveType->description)
                                <div class="text-xs text-gray-500">
                                    {{ $leaveType->description }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $leaveType->default_days_per_year ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($leaveType->requires_document)
                                <span class="rounded bg-yellow-100 px-2 py-1 text-xs text-yellow-800">
                                    ต้องแนบ
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                    ไม่บังคับ
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $leaveType->leave_requests_count }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($leaveType->is_active)
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
                                @can('leave.update')
                                    <a href="{{ route('leave-types.edit', $leaveType) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('leave.delete')
                                    <form method="POST"
                                          action="{{ route('leave-types.destroy', $leaveType) }}"
                                          onsubmit="return confirm('ยืนยันการลบประเภทการลานี้?')">
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
                        <td colspan="7" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบข้อมูลประเภทการลา
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $leaveTypes->links() }}
    </div>
</x-layouts.app>
