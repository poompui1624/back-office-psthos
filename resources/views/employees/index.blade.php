<x-layouts.app title="ทะเบียนบุคลากร">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ทะเบียนบุคลากร</h1>
            <p class="text-sm text-gray-600">จัดการข้อมูลบุคลากร หน่วยงาน และตำแหน่ง</p>
        </div>

        @can('employee.create')
            <a href="{{ route('employees.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่มบุคลากร
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
        <form method="GET" action="{{ route('employees.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัส / ชื่อ / หน่วยงาน / ตำแหน่ง / เบอร์โทร"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('employees.index') }}"
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
                    <th class="border px-4 py-2 text-left">ชื่อ-สกุล</th>
                    <th class="border px-4 py-2 text-left">หน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">ตำแหน่ง</th>
                    <th class="border px-4 py-2 text-left">โทรศัพท์</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $employee->employee_code }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $employee->full_name }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $employee->department?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $employee->position?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $employee->phone ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($employee->status === 'active')
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                    ปฏิบัติงาน
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                    {{ $employee->status }}
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('employee.update')
                                    <a href="{{ route('employees.edit', $employee) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('employee.delete')
                                    <form method="POST"
                                          action="{{ route('employees.destroy', $employee) }}"
                                          onsubmit="return confirm('ยืนยันการลบบุคลากรนี้?')">
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
                            ไม่พบข้อมูลบุคลากร
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $employees->links() }}
    </div>
</x-layouts.app>
