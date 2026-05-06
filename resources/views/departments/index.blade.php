<x-layouts.app title="ทะเบียนหน่วยงาน">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ทะเบียนหน่วยงาน</h1>
            <p class="text-sm text-gray-600">จัดการหน่วยงาน กลุ่มงาน และแผนกภายในโรงพยาบาล</p>
        </div>

        @can('department.create')
            <a href="{{ route('departments.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่มหน่วยงาน
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
        <form method="GET" action="{{ route('departments.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัส / ชื่อหน่วยงาน / ประเภท"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('departments.index') }}"
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
                    <th class="border px-4 py-2 text-left">ชื่อหน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">หน่วยงานแม่</th>
                    <th class="border px-4 py-2 text-left">ประเภท</th>
                    <th class="border px-4 py-2 text-center">บุคลากร</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departments as $department)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $department->code }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $department->name }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $department->parent?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $department->type ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $department->employees_count }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($department->is_active)
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">ใช้งาน</span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">ปิดใช้งาน</span>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('department.update')
                                    <a href="{{ route('departments.edit', $department) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('department.delete')
                                    <form method="POST"
                                          action="{{ route('departments.destroy', $department) }}"
                                          onsubmit="return confirm('ยืนยันการลบหน่วยงานนี้?')">
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
                            ไม่พบข้อมูลหน่วยงาน
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $departments->links() }}
    </div>
</x-layouts.app>
