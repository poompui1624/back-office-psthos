<x-layouts.app title="ตั้งค่าเงินเดือน">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">ตั้งค่าเงินเดือน</h1>
            <p class="text-sm text-gray-600">
                ตั้งค่าเงินเดือน รายได้ รายการหัก และอัตราหักจากเวลาทำงาน
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('payroll-periods.index') }}"
               class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                รอบเงินเดือน
            </a>

            @can('payroll.create')
                <a href="{{ route('salary-profiles.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    เพิ่มข้อมูลเงินเดือน
                </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 rounded bg-white p-4 shadow">
        <form method="GET" action="{{ route('salary-profiles.index') }}" class="flex gap-2">
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="ค้นหารหัส / ชื่อบุคลากร"
                   class="w-full rounded border-gray-300">

            <button type="submit"
                    class="rounded bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
                ค้นหา
            </button>

            <a href="{{ route('salary-profiles.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ล้าง
            </a>
        </form>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">บุคลากร</th>
                    <th class="border px-4 py-2 text-left">หน่วยงาน</th>
                    <th class="border px-4 py-2 text-right">เงินเดือน</th>
                    <th class="border px-4 py-2 text-right">รายได้รวมตั้งต้น</th>
                    <th class="border px-4 py-2 text-right">หักประจำ</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($profiles as $profile)
                    @php
                        $gross = $profile->base_salary
                            + $profile->position_allowance
                            + $profile->professional_allowance
                            + $profile->other_allowance;

                        $deduct = $profile->social_security
                            + $profile->tax
                            + $profile->provident_fund
                            + $profile->other_deduction;
                    @endphp

                    <tr>
                        <td class="border px-4 py-2">
                            {{ $profile->employee?->employee_code }}
                            <div class="text-xs text-gray-500">
                                {{ $profile->employee?->full_name ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $profile->employee?->department?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-right">
                            {{ number_format($profile->base_salary, 2) }}
                        </td>

                        <td class="border px-4 py-2 text-right">
                            {{ number_format($gross, 2) }}
                        </td>

                        <td class="border px-4 py-2 text-right">
                            {{ number_format($deduct, 2) }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($profile->is_active)
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
                                @can('payroll.update')
                                    <a href="{{ route('salary-profiles.edit', $profile) }}"
                                       class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600">
                                        แก้ไข
                                    </a>
                                @endcan

                                @can('payroll.delete')
                                    <form method="POST"
                                          action="{{ route('salary-profiles.destroy', $profile) }}"
                                          onsubmit="return confirm('ยืนยันการลบข้อมูลเงินเดือนนี้?')">
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
                            ยังไม่มีข้อมูลเงินเดือน
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $profiles->links() }}
    </div>
</x-layouts.app>
