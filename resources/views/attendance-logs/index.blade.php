<x-layouts.app title="ข้อมูลเวลาสแกน">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ข้อมูลเวลาสแกน</h1>
            <p class="text-sm text-gray-600">
                รายการเวลาที่นำเข้าจากเครื่องสแกนนิ้วมือ
            </p>
        </div>

        <div class="flex gap-2">
            @can('attendance.import')
                <a href="{{ route('attendance-logs.import-form') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    นำเข้า CSV
                </a>
            @endcan

            @can('attendance.view')
                <a href="{{ route('attendance-devices.index') }}"
                   class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                    เครื่องสแกน
                </a>
            @endcan
        </div>
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
        <form method="GET" action="{{ route('attendance-logs.index') }}" class="grid gap-3 md:grid-cols-5">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัสพนักงาน / ชื่อ / เครื่องสแกน"
                   class="rounded border-gray-300 md:col-span-2">

            <input type="date"
                   name="date_from"
                   value="{{ $dateFrom }}"
                   class="rounded border-gray-300">

            <input type="date"
                   name="date_to"
                   value="{{ $dateTo }}"
                   class="rounded border-gray-300">

            <div class="flex gap-2">
                <button type="submit"
                        class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                    ค้นหา
                </button>

                <a href="{{ route('attendance-logs.index') }}"
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
                    <th class="border px-4 py-2 text-left">วันเวลา</th>
                    <th class="border px-4 py-2 text-left">รหัสพนักงาน</th>
                    <th class="border px-4 py-2 text-left">บุคลากร</th>
                    <th class="border px-4 py-2 text-left">เครื่องสแกน</th>
                    <th class="border px-4 py-2 text-left">ประเภท</th>
                    <th class="border px-4 py-2 text-left">Verify</th>
                    <th class="border px-4 py-2 text-left">Source</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="border px-4 py-2 whitespace-nowrap">
                            {{ $log->scan_time?->format('Y-m-d H:i:s') }}
                            <div class="text-xs text-gray-500">
                                วันที่: {{ $log->scan_date?->format('Y-m-d') }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $log->employee_code ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            @if ($log->employee)
                                {{ $log->employee->full_name }}
                                <div class="text-xs text-gray-500">
                                    {{ $log->employee->department?->name ?? '-' }}
                                </div>
                            @else
                                <span class="text-red-600">ไม่พบในทะเบียนบุคลากร</span>
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            @if ($log->device)
                                {{ $log->device->code }}
                                <div class="text-xs text-gray-500">
                                    {{ $log->device->name }}
                                </div>
                            @else
                                {{ $log->device_code ?? '-' }}
                            @endif
                        </td>

                        <td class="border px-4 py-2">
                            {{ $log->scan_type ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $log->verify_type ?? '-' }}
                        </td>

                        <td class="border px-4 py-2">
                            {{ $log->source }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border px-4 py-6 text-center text-gray-500">
                            ไม่พบข้อมูลเวลาสแกน
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</x-layouts.app>
