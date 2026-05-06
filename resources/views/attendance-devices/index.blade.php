<x-layouts.app title="เครื่องสแกนนิ้วมือ">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">เครื่องสแกนนิ้วมือ</h1>
            <p class="text-sm text-gray-600">
                จัดการอุปกรณ์บันทึกเวลาทำงาน
            </p>
        </div>

        @can('attendance.create')
            <a href="{{ route('attendance-devices.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่มเครื่องสแกน
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
        <form method="GET" action="{{ route('attendance-devices.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัส / ชื่อเครื่อง / สถานที่ / IP"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('attendance-devices.index') }}"
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
                    <th class="border px-4 py-2 text-left">ชื่อเครื่อง</th>
                    <th class="border px-4 py-2 text-left">สถานที่</th>
                    <th class="border px-4 py-2 text-left">IP Address</th>
                    <th class="border px-4 py-2 text-left">ยี่ห้อ / รุ่น</th>
                    <th class="border px-4 py-2 text-center">จำนวน Log</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($devices as $device)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $device->code }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $device->name }}

                            @if ($device->remark)
                                <div class="text-xs text-gray-500">
                                    {{ $device->remark }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $device->location ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $device->ip_address ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $device->brand ?? '-' }}
                            <div class="text-xs text-gray-500">
                                {{ $device->model ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $device->logs_count }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($device->is_active)
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
                                @can('attendance.update')
                                    <a href="{{ route('attendance-devices.edit', $device) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('attendance.delete')
                                    <form method="POST"
                                          action="{{ route('attendance-devices.destroy', $device) }}"
                                          onsubmit="return confirm('ยืนยันการลบเครื่องสแกนนี้?')">
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
                            ไม่พบข้อมูลเครื่องสแกนนิ้วมือ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $devices->links() }}
    </div>
</x-layouts.app>
