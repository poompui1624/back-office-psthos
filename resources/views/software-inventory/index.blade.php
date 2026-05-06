<x-layouts.app title="Software Inventory">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Software Inventory</h1>
        <p class="text-sm text-gray-600">
            รายงานโปรแกรมที่ติดตั้งในเครื่องคอมพิวเตอร์จากข้อมูล Agent ล่าสุด
        </p>
    </div>

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('software-inventory.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหาชื่อโปรแกรม / version / publisher"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('software-inventory.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ล้าง
            </a>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">ชื่อ Software</th>
                    <th class="border px-4 py-2 text-left">Version</th>
                    <th class="border px-4 py-2 text-left">Publisher</th>
                    <th class="border px-4 py-2 text-center">จำนวนเครื่อง</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($softwareItems as $software)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $software['name'] }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $software['version'] ?: '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $software['publisher'] ?: '-' }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $software['computer_count'] }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <a href="{{ route('software-inventory.computers', [
                                'name' => $software['name'],
                                'version' => $software['version'],
                                'publisher' => $software['publisher'],
                            ]) }}"
                               class="rounded bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-900">
                                ดูเครื่องที่ติดตั้ง
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบข้อมูล Software Inventory
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $softwareItems->links() }}
    </div>
</x-layouts.app>
