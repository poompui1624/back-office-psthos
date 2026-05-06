<x-layouts.app title="ทะเบียนพัสดุ">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ทะเบียนพัสดุ</h1>
            <p class="text-sm text-gray-600">จัดการข้อมูลพัสดุรวมทั้งโรงพยาบาล</p>
        </div>

        @can('asset.create')
            <a href="{{ route('assets.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่มพัสดุ
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('assets.index') }}" class="grid gap-3 md:grid-cols-4">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="ค้นหารหัส / ชื่อ / serial / หน่วยงาน"
                   class="rounded border-gray-300 md:col-span-2">

            <select name="status" class="rounded border-gray-300">
                <option value="">ทุกสถานะ</option>
                <option value="active" @selected($status === 'active')>ใช้งาน</option>
                <option value="repairing" @selected($status === 'repairing')>กำลังซ่อม</option>
                <option value="broken" @selected($status === 'broken')>ชำรุด</option>
                <option value="disposed" @selected($status === 'disposed')>จำหน่าย</option>
                <option value="lost" @selected($status === 'lost')>สูญหาย</option>
            </select>

            <div class="flex gap-2">
                <button class="rounded bg-gray-800 px-4 py-2 text-white">ค้นหา</button>
                <a href="{{ route('assets.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-gray-700">ล้าง</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">รหัสพัสดุ</th>
                    <th class="border px-4 py-2 text-left">ชื่อพัสดุ</th>
                    <th class="border px-4 py-2 text-left">หมวดหมู่</th>
                    <th class="border px-4 py-2 text-left">หน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">ผู้รับผิดชอบ</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $asset)
                    <tr>
                        <td class="border px-4 py-2">{{ $asset->asset_code }}</td>
                        <td class="border px-4 py-2">
                            {{ $asset->name }}
                            <div class="text-xs text-gray-500">
                                {{ $asset->brand }} {{ $asset->model }}
                            </div>
                            @if ($asset->serial_number)
                                <div class="text-xs text-gray-500">
                                    S/N: {{ $asset->serial_number }}
                                </div>
                            @endif
                        </td>
                        <td class="border px-4 py-2">{{ $asset->category?->name ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $asset->department?->name ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $asset->responsibleEmployee?->full_name ?? '-' }}</td>
                        <td class="border px-4 py-2 text-center">
                            <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                {{ $asset->status }}
                            </span>
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('asset.movement')
                                    <a href="{{ route('asset-movements.create', ['asset_id' => $asset->id]) }}"
                                    class="rounded bg-blue-600 px-3 py-1 text-sm text-white">
                                        โอนย้าย
                                    </a>
                                @endcan
                                @can('asset.update')
                                    <a href="{{ route('assets.edit', $asset) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('asset.delete')
                                    <form method="POST"
                                          action="{{ route('assets.destroy', $asset) }}"
                                          onsubmit="return confirm('ยืนยันการลบพัสดุนี้?')">
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
                        <td colspan="7" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบข้อมูลพัสดุ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $assets->links() }}</div>
</x-layouts.app>
