<x-layouts.app title="หมวดหมู่พัสดุ">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">หมวดหมู่พัสดุ</h1>
            <p class="text-sm text-gray-600">จัดการประเภทของพัสดุ</p>
        </div>

        @can('asset.create')
            <a href="{{ route('asset-categories.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่มหมวดหมู่
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('asset-categories.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="ค้นหารหัส / ชื่อหมวดหมู่"
                   class="w-full rounded border-gray-300">

            <button class="rounded bg-gray-800 px-4 py-2 text-white">ค้นหา</button>

            <a href="{{ route('asset-categories.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700">ล้าง</a>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">รหัส</th>
                    <th class="border px-4 py-2 text-left">ชื่อหมวดหมู่</th>
                    <th class="border px-4 py-2 text-center">จำนวนพัสดุ</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td class="border px-4 py-2">{{ $category->code }}</td>
                        <td class="border px-4 py-2">{{ $category->name }}</td>
                        <td class="border px-4 py-2 text-center">{{ $category->assets_count }}</td>
                        <td class="border px-4 py-2 text-center">
                            @if ($category->is_active)
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">ใช้งาน</span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('asset.update')
                                    <a href="{{ route('asset-categories.edit', $category) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('asset.delete')
                                    <form method="POST"
                                          action="{{ route('asset-categories.destroy', $category) }}"
                                          onsubmit="return confirm('ยืนยันการลบหมวดหมู่นี้?')">
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
                        <td colspan="5" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบข้อมูล
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
</x-layouts.app>
