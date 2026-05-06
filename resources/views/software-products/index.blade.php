<x-layouts.app title="Software Products">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Software Products</h1>
            <p class="text-sm text-gray-600">ทะเบียนโปรแกรมและผู้ผลิต</p>
        </div>

        @can('software.create')
            <a href="{{ route('software-products.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                เพิ่ม Software
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
        <form method="GET" action="{{ route('software-products.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="ค้นหาชื่อ Software / Vendor / Category"
                   class="w-full rounded border-gray-300">

            <button class="rounded bg-gray-800 px-4 py-2 text-white">ค้นหา</button>

            <a href="{{ route('software-products.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700">ล้าง</a>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">ชื่อ Software</th>
                    <th class="border px-4 py-2 text-left">Vendor</th>
                    <th class="border px-4 py-2 text-left">Category</th>
                    <th class="border px-4 py-2 text-center">License</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td class="border px-4 py-2">{{ $product->name }}</td>
                        <td class="border px-4 py-2">{{ $product->vendor ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $product->category ?? '-' }}</td>
                        <td class="border px-4 py-2 text-center">{{ $product->licenses_count }}</td>
                        <td class="border px-4 py-2 text-center">
                            @if ($product->is_active)
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">ใช้งาน</span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                @can('software.update')
                                    <a href="{{ route('software-products.edit', $product) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('software.delete')
                                    <form method="POST"
                                          action="{{ route('software-products.destroy', $product) }}"
                                          onsubmit="return confirm('ยืนยันการลบ Software นี้?')">
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
                            ไม่พบข้อมูล Software
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</x-layouts.app>
