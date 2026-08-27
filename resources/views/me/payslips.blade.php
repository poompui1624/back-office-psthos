<x-layouts.app>
    @include('me._header', ['employee' => $employee, 'title' => 'สลิปเงินเดือนของฉัน', 'subtitle' => 'สลิปย้อนหลังทุกรอบ'])

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">รอบเงินเดือน</th>
                        <th class="px-4 py-3 text-right font-semibold">รายได้รวม</th>
                        <th class="px-4 py-3 text-right font-semibold">หักรวม</th>
                        <th class="px-4 py-3 text-right font-semibold">รับสุทธิ</th>
                        <th class="px-4 py-3 text-center font-semibold">จัดการ</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($payslips as $payslip)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $payslip->payrollPeriod?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) $payslip->gross_income, 2) }}</td>
                            <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) $payslip->total_deduction, 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ number_format((float) $payslip->net_pay, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('payslips.print', $payslip) }}"
                                   class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">
                                    พิมพ์
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                ยังไม่มีสลิปเงินเดือนของคุณ
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $payslips->links() }}</div>
</x-layouts.app>
