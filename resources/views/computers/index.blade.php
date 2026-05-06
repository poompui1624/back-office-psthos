<x-layouts.app title="ทะเบียนคอมพิวเตอร์">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ทะเบียนคอมพิวเตอร์</h1>
            <p class="text-sm text-gray-600">จัดการเครื่องคอมพิวเตอร์ภายในโรงพยาบาล</p>
        </div>

        @can('computer.create')
            <a href="{{ route('computers.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่มคอมพิวเตอร์
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('computers.index') }}" class="grid gap-3 md:grid-cols-4">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหา hostname / IP / serial / หน่วยงาน"
                   class="rounded border-gray-300 md:col-span-2">

            <select name="status" class="rounded border-gray-300">
                <option value="">ทุกสถานะ</option>
                <option value="active" @selected($status === 'active')>ใช้งาน</option>
                <option value="inactive" @selected($status === 'inactive')>ปิดใช้งาน</option>
                <option value="repairing" @selected($status === 'repairing')>กำลังซ่อม</option>
                <option value="disposed" @selected($status === 'disposed')>จำหน่าย</option>
            </select>

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                    ค้นหา
                </button>

                <a href="{{ route('computers.index') }}"
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
                    <th class="border px-4 py-2 text-left">Hostname</th>
                    <th class="border px-4 py-2 text-left">Asset</th>
                    <th class="border px-4 py-2 text-left">IP / MAC</th>
                    <th class="border px-4 py-2 text-left">OS</th>
                    <th class="border px-4 py-2 text-left">หน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">ผู้รับผิดชอบ</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($computers as $computer)
                    <tr>
                        <td class="border px-4 py-2">
                            <div class="font-medium">{{ $computer->hostname }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $computer->manufacturer }} {{ $computer->model }}
                            </div>
                            @if ($computer->serial_number)
                                <div class="text-xs text-gray-500">
                                    S/N: {{ $computer->serial_number }}
                                </div>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            @if ($computer->asset)
                                {{ $computer->asset->asset_code }}
                                <div class="text-xs text-gray-500">
                                    {{ $computer->asset->name }}
                                </div>
                            @else
                                -
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $computer->ip_address ?? '-' }}
                            <div class="text-xs text-gray-500">
                                {{ $computer->mac_address ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $computer->os_name ?? '-' }}
                            <div class="text-xs text-gray-500">
                                {{ $computer->os_version ?? '' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $computer->department?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $computer->responsibleEmployee?->full_name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                {{ $computer->status }}
                            </span>
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('computer.view')
                                    <a href="{{ route('computers.show', $computer) }}"
                                    class="rounded bg-gray-800 px-3 py-1 text-sm text-white">
                                        รายละเอียด
                                    </a>
                                @endcan

                                @can('computer.update')
                                    <a href="{{ route('computers.edit', $computer) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('computer.delete')
                                    <form method="POST"
                                          action="{{ route('computers.destroy', $computer) }}"
                                          onsubmit="return confirm('ยืนยันการลบคอมพิวเตอร์นี้?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="rounded bg-red-600 px-3 py-1 text-sm text-white">
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
                            ไม่พบข้อมูลคอมพิวเตอร์
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $computers->links() }}
    </div>
</x-layouts.app>
