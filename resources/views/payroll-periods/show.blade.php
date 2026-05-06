<x-layouts.app title="รายละเอียดรอบเงินเดือน">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                {{ $payrollPeriod->name }}
            </h1>
            <p class="text-sm text-gray-600">
                {{ $payrollPeriod->start_date?->format('Y-m-d') }}
                ถึง
                {{ $payrollPeriod->end_date?->format('Y-m-d') }}
            </p>
        </div>

        <div class="flex gap-2">
            @if ($payrollPeriod->status !== 'closed')
                @can('payroll.generate')
                    <form method="POST"
                          action="{{ route('payroll-periods.generate', $payrollPeriod) }}"
                          onsubmit="return confirm('ยืนยันการ Generate สลิปเงินเดือนรอบนี้?')">
                        @csrf

                        <button type="submit"
                                class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            Generate สลิป
                        </button>
                    </form>
                @endcan

                @can('payroll.update')
                    <form method="POST"
                          action="{{ route('payroll-periods.close', $payrollPeriod) }}"
                          onsubmit="return confirm('ยืนยันการปิดรอบเงินเดือนนี้? หลังปิดแล้วไม่ควร Generate ใหม่')">
                        @csrf

                        <button type="submit"
                                class="rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                            ปิดรอบ
                        </button>
                    </form>
                @endcan
            @endif

            <a href="{{ route('payroll-periods.index') }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ย้อนกลับ
            </a>
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

    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">สถานะ</div>
            <div class="mt-2 text-xl font-bold">
                {{ $payrollPeriod->status }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">จำนวนสลิป</div>
            <div class="mt-2 text-xl font-bold">
                {{ $payslips->total() }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">สร้างโดย</div>
            <div class="mt-2 text-xl font-bold">
                {{ $payrollPeriod->creator?->name ?? '-' }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">Generate ล่าสุด</div>
            <div class="mt-2 text-xl font-bold">
                {{ $payrollPeriod->generated_at?->format('Y-m-d') ?? '-' }}
            </div>
        </div>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">รายได้รวม</div>
            <div class="mt-2 text-2xl font-bold text-green-700">
                {{ number_format($payrollPeriod->payslips()->sum('gross_income'), 2) }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">รายการหักรวม</div>
            <div class="mt-2 text-2xl font-bold text-red-700">
                {{ number_format($payrollPeriod->payslips()->sum('total_deduction'), 2) }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">จ่ายสุทธิรวม</div>
            <div class="mt-2 text-2xl font-bold text-blue-700">
                {{ number_format($payrollPeriod->payslips()->sum('net_pay'), 2) }}
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded bg-white shadow">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">บุคลากร</th>
                    <th class="border px-4 py-2 text-left">หน่วยงาน</th>
                    <th class="border px-4 py-2 text-right">รายได้</th>
                    <th class="border px-4 py-2 text-right">รายการหัก</th>
                    <th class="border px-4 py-2 text-right">สุทธิ</th>
                    <th class="border px-4 py-2 text-center">มาสาย</th>
                    <th class="border px-4 py-2 text-center">กลับก่อน</th>
                    <th class="border px-4 py-2 text-center">ขาดงาน</th>
                    <th class="border px-4 py-2 text-center">สถานะ</th>
                    <th class="border px-4 py-2 text-center">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($payslips as $payslip)
                    <tr>
                        <td class="border px-4 py-2">
                            {{ $payslip->employee?->employee_code }}
                            <div class="text-xs text-gray-500">
                                {{ $payslip->employee?->full_name ?? '-' }}
                            </div>
                        </td>

                        <td class="border px-4 py-2">
                            {{ $payslip->employee?->department?->name ?? '-' }}
                        </td>

                        <td class="border px-4 py-2 text-right">
                            {{ number_format($payslip->gross_income, 2) }}
                        </td>

                        <td class="border px-4 py-2 text-right">
                            {{ number_format($payslip->total_deduction, 2) }}
                        </td>

                        <td class="border px-4 py-2 text-right font-bold">
                            {{ number_format($payslip->net_pay, 2) }}
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $payslip->late_minutes }} นาที
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $payslip->early_leave_minutes }} นาที
                        </td>

                        <td class="border px-4 py-2 text-center">
                            {{ $payslip->absent_days }} วัน
                        </td>

                        <td class="border px-4 py-2 text-center">
                            @if ($payslip->status === 'draft')
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800">
                                    ร่าง
                                </span>
                            @elseif ($payslip->status === 'approved')
                                <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-800">
                                    อนุมัติแล้ว
                                </span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800">
                                    {{ $payslip->status }}
                                </span>
                            @endif
                        </td>

                        <td class="border px-4 py-2 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('payslips.show', $payslip) }}"
                                class="rounded bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-900">
                                    ดูสลิป
                                </a>

                                <a href="{{ route('payslips.print', $payslip) }}"
                                target="_blank"
                                class="rounded bg-blue-600 px-3 py-1 text-sm text-white hover:bg-blue-700">
                                    พิมพ์
                                </a>
                            </div>
                        </td>
                    </tr>

                    <tr class="bg-gray-50">
                        <td colspan="10" class="border px-4 py-3">
                            <details>
                                <summary class="cursor-pointer text-sm font-medium text-gray-700">
                                    ดูรายละเอียดรายการเงินเดือน
                                </summary>

                                <div class="mt-3 grid gap-4 md:grid-cols-2">
                                    <div>
                                        <h3 class="mb-2 font-bold text-green-700">รายได้</h3>

                                        <table class="w-full border-collapse bg-white">
                                            <tbody>
                                                @foreach ($payslip->items()->where('type', 'income')->orderBy('sort_order')->get() as $item)
                                                    <tr>
                                                        <td class="border px-3 py-2">{{ $item->name }}</td>
                                                        <td class="border px-3 py-2 text-right">
                                                            {{ number_format($item->amount, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div>
                                        <h3 class="mb-2 font-bold text-red-700">รายการหัก</h3>

                                        <table class="w-full border-collapse bg-white">
                                            <tbody>
                                                @foreach ($payslip->items()->where('type', 'deduction')->orderBy('sort_order')->get() as $item)
                                                    <tr>
                                                        <td class="border px-3 py-2">
                                                            {{ $item->name }}

                                                            @if ($item->quantity > 1)
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $item->quantity }} x {{ number_format($item->unit_amount, 2) }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="border px-3 py-2 text-right">
                                                            {{ number_format($item->amount, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="border px-4 py-6 text-center text-gray-500">
                            ยังไม่มีสลิปเงินเดือนในรอบนี้
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payslips->links() }}
    </div>
</x-layouts.app>
