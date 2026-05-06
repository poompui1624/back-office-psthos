<x-layouts.app title="Software Licenses">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Software Licenses</h1>
            <p class="text-sm text-gray-600">ทะเบียน License วันหมดอายุ ต่ออายุ และยกเลิก</p>
        </div>

        @can('software.create')
            <a href="{{ route('software-licenses.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่ม License
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('software-licenses.index') }}" class="grid gap-3 md:grid-cols-4">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="ค้นหา Software / License / Vendor"
                   class="rounded border-gray-300 md:col-span-2">

            <select name="status" class="rounded border-gray-300">
                <option value="">ทุกสถานะ</option>
                <option value="active" @selected($status === 'active')>ใช้งาน</option>
                <option value="expired" @selected($status === 'expired')>หมดอายุ</option>
                <option value="renewed" @selected($status === 'renewed')>ต่ออายุแล้ว</option>
                <option value="cancelled" @selected($status === 'cancelled')>ยกเลิก</option>
            </select>

            <div class="flex gap-2">
                <button class="rounded bg-gray-800 px-4 py-2 text-white">ค้นหา</button>
                <a href="{{ route('software-licenses.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700">ล้าง</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">Software</th>
                    <th class="border px-4 py-2 text-left">License</th>
                    <th class="border px-4 py-2 text-center">Seats</th>
                    <th class="border px-4 py-2 text-left">วันหมดอายุ</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($licenses as $license)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $license->product?->name }}
                            <div class="text-xs text-gray-500">
                                {{ $license->product?->vendor ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $license->license_name ?? '-' }}
                            <div class="text-xs text-gray-500">
                                {{ $license->license_type ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $license->used_seats }} / {{ $license->total_seats }}
                        </td>

                        <td class="border px-4 py-2">
                            @if ($license->expire_date)
                            {{ $license->expire_date->format('Y-m-d') }}

                            @if ($license->is_expired)
                                <span class="ml-2 rounded bg-red-100 px-2 py-1 text-xs text-red-800">
                                    หมดอายุ
                                </span>
                            @elseif ($license->is_expiring_soon)
                                <span class="ml-2 rounded bg-yellow-100 px-2 py-1 text-xs text-yellow-800">
                                    ใกล้หมดอายุ
                                </span>
                            @endif

                            @if ($license->last_expire_notified_at)
                                <div class="mt-1 text-xs text-gray-500">
                                    แจ้งเตือนล่าสุด:
                                    {{ $license->last_expire_notified_at->format('Y-m-d H:i') }}
                                </div>
                            @endif
                        @else
                            -
                        @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                {{ $license->status }}
                            </span>
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('software.update')
                                <a href="{{ route('software-licenses.renew-form', $license) }}"
                                class="rounded bg-green-600 px-3 py-1 text-sm text-white">
                                    ต่ออายุ
                                </a>
                                @endcan

                                @if ($license->status !== 'cancelled')
                                    <a href="{{ route('software-licenses.cancel-form', $license) }}"
                                    class="rounded bg-red-700 px-3 py-1 text-sm text-white">
                                        ยกเลิก
                                    </a>
                                @endif
                                @can('software.update')
                                    <a href="{{ route('software-licenses.edit', $license) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('software.delete')
                                    <form method="POST"
                                          action="{{ route('software-licenses.destroy', $license) }}"
                                          onsubmit="return confirm('ยืนยันการลบ License นี้?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="rounded bg-red-600 px-3 py-1 text-sm text-white">
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
                            ไม่พบข้อมูล License
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $licenses->links() }}</div>
</x-layouts.app>
