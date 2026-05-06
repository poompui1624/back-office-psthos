<x-layouts.app title="ผู้ใช้งานระบบ">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ผู้ใช้งานระบบ</h1>
            <p class="text-sm text-gray-600">จัดการบัญชี Login และสิทธิ์การใช้งาน</p>
        </div>

        @can('user.create')
            <a href="{{ route('users.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่มผู้ใช้งาน
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
        <form method="GET" action="{{ route('users.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหาชื่อ / อีเมล / รหัสบุคลากร"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('users.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ล้าง
            </a>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">ชื่อผู้ใช้</th>
                    <th class="border px-4 py-2 text-left">อีเมล</th>
                    <th class="border px-4 py-2 text-left">บุคลากร</th>
                    <th class="border px-4 py-2 text-left">Role</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $user->name }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $user->email }}
                        </td>

                        <td class="border px-4 py-2">
                            @if ($user->employee)
                                {{ $user->employee->employee_code }} -
                                {{ $user->employee->full_name }}
                                <div class="text-xs text-gray-500">
                                    {{ $user->employee->department?->name ?? '-' }}
                                </div>
                            @else
                                -
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            @forelse ($user->roles as $role)
                                <span class="mb-1 inline-block rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-gray-400">ไม่มี role</span>
                            @endforelse
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($user->is_active)
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
                                @can('user.update')
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('user.delete')
                                    <form method="POST"
                                          action="{{ route('users.destroy', $user) }}"
                                          onsubmit="return confirm('ยืนยันการลบผู้ใช้งานนี้?')">
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
                        <td colspan="6" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบข้อมูลผู้ใช้งาน
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</x-layouts.app>
