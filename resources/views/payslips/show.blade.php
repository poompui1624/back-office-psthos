<x-layouts.app title="รายละเอียดสลิปเงินเดือน">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">รายละเอียดสลิปเงินเดือน</h1>
            <p class="text-sm text-gray-600">
                {{ $payslip->employee?->employee_code }}
                -
                {{ $payslip->employee?->full_name }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('payslips.print', $payslip) }}"
               target="_blank"
               class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                พิมพ์สลิป
            </a>

            <a href="{{ route('payroll-periods.show', $payslip->payrollPeriod) }}"
               class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                ย้อนกลับ
            </a>
        </div>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">รอบเงินเดือน</div>
            <div class="mt-2 font-bold">
                {{ $payslip->payrollPeriod?->name }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">รายได้รวม</div>
            <div class="mt-2 text-xl font-bold text-green-700">
                {{ number_format($payslip->gross_income, 2) }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">รายการหักรวม</div>
            <div class="mt-2 text-xl font-bold text-red-700">
                {{ number_format($payslip->total_deduction, 2) }}
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">จ่ายสุทธิ</div>
            <div class="mt-2 text-xl font-bold text-blue-700">
                {{ number_format($payslip->net_pay, 2) }}
            </div>
        </div>
    </div>

    <div class="mb-6 rounded bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold">ข้อมูลบุคลากร</h2>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <div class="text-sm text-gray-500">รหัสบุคลากร</div>
                <div class="font-medium">{{ $payslip->employee?->employee_code ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ชื่อ - สกุล</div>
                <div class="font-medium">{{ $payslip->employee?->full_name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">หน่วยงาน</div>
                <div class="font-medium">{{ $payslip->employee?->department?->name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500">ตำแหน่ง</div>
                <div class="font-medium">{{ $payslip->employee?->position?->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">มาสาย</div>
            <div class="mt-2 text-xl font-bold">
                {{ $payslip->late_minutes }} นาที
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">กลับก่อน</div>
            <div class="mt-2 text-xl font-bold">
                {{ $payslip->early_leave_minutes }} นาที
            </div>
        </div>

        <div class="rounded bg-white p-5 shadow">
            <div class="text-sm text-gray-500">ขาดงาน / ไม่พบสแกน</div>
            <div class="mt-2 text-xl font-bold">
                {{ $payslip->absent_days }} วัน
            </div>
        </div>
    </div>

    @php
        $incomeItems = $payslip->items->where('type', 'income');
        $deductionItems = $payslip->items->where('type', 'deduction');
    @endphp

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold text-green-700">รายได้</h2>

            <table class="w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">รายการ</th>
                        <th class="border px-4 py-2 text-right">จำนวนเงิน</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($incomeItems as $item)
                        <tr>
                            <td class="border px-4 py-2">
                                {{ $item->name }}

                                @if ($item->quantity > 1)
                                    <div class="text-xs text-gray-500">
                                        {{ $item->quantity }} x {{ number_format($item->unit_amount, 2) }}
                                    </div>
                                @endif
                            </td>

                            <td class="border px-4 py-2 text-right">
                                {{ number_format($item->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="border px-4 py-4 text-center text-gray-500">
                                ไม่มีรายการรายได้
                            </td>
                        </tr>
                    @endforelse

                    <tr class="bg-green-50 font-bold">
                        <td class="border px-4 py-2">รวมรายได้</td>
                        <td class="border px-4 py-2 text-right">
                            {{ number_format($payslip->gross_income, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-bold text-red-700">รายการหัก</h2>

            <table class="w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">รายการ</th>
                        <th class="border px-4 py-2 text-right">จำนวนเงิน</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($deductionItems as $item)
                        <tr>
                            <td class="border px-4 py-2">
                                {{ $item->name }}

                                @if ($item->quantity > 1)
                                    <div class="text-xs text-gray-500">
                                        {{ $item->quantity }} x {{ number_format($item->unit_amount, 2) }}
                                    </div>
                                @endif
                            </td>

                            <td class="border px-4 py-2 text-right">
                                {{ number_format($item->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="border px-4 py-4 text-center text-gray-500">
                                ไม่มีรายการหัก
                            </td>
                        </tr>
                    @endforelse

                    <tr class="bg-red-50 font-bold">
                        <td class="border px-4 py-2">รวมรายการหัก</td>
                        <td class="border px-4 py-2 text-right">
                            {{ number_format($payslip->total_deduction, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 rounded bg-blue-50 p-6 text-right">
        <div class="text-sm text-blue-700">ยอดสุทธิที่ต้องจ่าย</div>
        <div class="mt-2 text-3xl font-bold text-blue-900">
            {{ number_format($payslip->net_pay, 2) }} บาท
        </div>
    </div>
</x-layouts.app>
