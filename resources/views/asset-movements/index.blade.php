<x-layouts.app title="ประวัติการโอนย้ายพัสดุ">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ประวัติการโอนย้ายพัสดุ</h1>
            <p class="text-sm text-gray-600">ติดตามการย้ายพัสดุระหว่างหน่วยงานและผู้รับผิดชอบ</p>
        </div>

        @can('asset.movement')
            <a href="{{ route('asset-movements.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                โอนย้ายพัสดุ
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('asset-movements.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัสพัสดุ / ชื่อพัสดุ / หน่วยงาน / เหตุผล"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('asset-movements.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ล้าง
            </a>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">วันที่โอนย้าย</th>
                    <th class="border px-4 py-2 text-left">พัสดุ</th>
                    <th class="border px-4 py-2 text-left">จากหน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">ไปหน่วยงาน</th>
                    <th class="border px-4 py-2 text-left">ผู้รับผิดชอบเดิม</th>
                    <th class="border px-4 py-2 text-left">ผู้รับผิดชอบใหม่</th>
                    <th class="border px-4 py-2 text-left">ผู้บันทึก</th>
                    <th class="border px-4 py-2 text-left">เหตุผล</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr>
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ $movement->moved_at?->format('Y-m-d') }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $movement->asset?->asset_code }}
                            <div class="text-xs text-gray-500">
                                {{ $movement->asset?->name }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $movement->fromDepartment?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $movement->toDepartment?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $movement->fromEmployee?->full_name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $movement->toEmployee?->full_name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $movement->movedBy?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $movement->reason ?? '-' }}

                            @if ($movement->remark)
                                <div class="text-xs text-gray-500">
                                    {{ $movement->remark }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบประวัติการโอนย้ายพัสดุ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $movements->links() }}
    </div>
</x-layouts.app>
