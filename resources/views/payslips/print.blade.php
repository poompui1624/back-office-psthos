<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>สลิปเงินเดือน {{ $payslip->employee?->employee_code }}</title>

    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            color: #111827;
            font-size: 14px;
        }

        .page {
            width: 800px;
            margin: 0 auto;
            padding: 24px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
        }

        .subtitle {
            margin-top: 6px;
            color: #4b5563;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 24px;
            margin-bottom: 20px;
        }

        .label {
            color: #6b7280;
            font-size: 12px;
        }

        .value {
            font-weight: bold;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            margin-top: 20px;
            border: 2px solid #111827;
            padding: 16px;
            text-align: right;
        }

        .net {
            font-size: 24px;
            font-weight: bold;
        }

        .signature-grid {
            margin-top: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #111827;
            padding-top: 8px;
        }

        .print-button {
            margin: 20px auto;
            width: 800px;
            text-align: right;
        }

        .print-button button {
            padding: 8px 16px;
            background: #111827;
            color: white;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
        }

        @media print {
            .print-button {
                display: none;
            }

            body {
                margin: 0;
            }

            .page {
                width: auto;
                padding: 16px;
            }
        }
    </style>
    @include('print._document-style')
</head>
<body>
    @include('print._document-header')
    <div class="print-button">
        <button onclick="window.print()">พิมพ์</button>
    </div>

    <div class="page">
        {{--
            The logo, hospital name, address, and phone come from
            print._document-header above, the way the other three print views
            do it. This block carries only what is specific to a payslip.
        --}}
        <div class="header">
            <div class="title">สลิปเงินเดือน</div>

            <div class="subtitle">
                รอบ {{ $payslip->payrollPeriod?->name }}
                |
                {{ $payslip->payrollPeriod?->start_date?->format('Y-m-d') }}
                ถึง
                {{ $payslip->payrollPeriod?->end_date?->format('Y-m-d') }}
            </div>
        </div>

        <div class="info-grid">
            <div>
                <div class="label">รหัสบุคลากร</div>
                <div class="value">{{ $payslip->employee?->employee_code ?? '-' }}</div>
            </div>

            <div>
                <div class="label">ชื่อ - สกุล</div>
                <div class="value">{{ $payslip->employee?->full_name ?? '-' }}</div>
            </div>

            <div>
                <div class="label">หน่วยงาน</div>
                <div class="value">{{ $payslip->employee?->department?->name ?? '-' }}</div>
            </div>

            <div>
                <div class="label">ตำแหน่ง</div>
                <div class="value">{{ $payslip->employee?->position?->name ?? '-' }}</div>
            </div>

            <div>
                <div class="label">มาสาย</div>
                <div class="value">{{ $payslip->late_minutes }} นาที</div>
            </div>

            <div>
                <div class="label">กลับก่อน</div>
                <div class="value">{{ $payslip->early_leave_minutes }} นาที</div>
            </div>

            <div>
                <div class="label">ขาดงาน / ไม่พบสแกน</div>
                <div class="value">{{ $payslip->absent_days }} วัน</div>
            </div>

            <div>
                <div class="label">วันที่สร้างสลิป</div>
                <div class="value">{{ $payslip->generated_at?->format('Y-m-d H:i') ?? '-' }}</div>
            </div>
        </div>

        @php
            $incomeItems = $payslip->items->where('type', 'income');
            $deductionItems = $payslip->items->where('type', 'deduction');
        @endphp

        <table>
            <thead>
                <tr>
                    <th colspan="2">รายได้</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($incomeItems as $item)
                    <tr>
                        <td>
                            {{ $item->name }}

                            @if ($item->quantity > 1)
                                <div style="font-size: 12px; color: #6b7280;">
                                    {{ $item->quantity }} x {{ number_format($item->unit_amount, 2) }}
                                </div>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td><strong>รวมรายได้</strong></td>
                    <td class="text-right"><strong>{{ number_format($payslip->gross_income, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="2">รายการหัก</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($deductionItems as $item)
                    <tr>
                        <td>
                            {{ $item->name }}

                            @if ($item->quantity > 1)
                                <div style="font-size: 12px; color: #6b7280;">
                                    {{ $item->quantity }} x {{ number_format($item->unit_amount, 2) }}
                                </div>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td><strong>รวมรายการหัก</strong></td>
                    <td class="text-right"><strong>{{ number_format($payslip->total_deduction, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="summary">
            <div>ยอดสุทธิที่ต้องจ่าย</div>
            <div class="net">{{ number_format($payslip->net_pay, 2) }} บาท</div>
        </div>

        <div class="signature-grid">
            <div>
                <div class="signature-line">ผู้จัดทำ</div>
            </div>

            <div>
                <div class="signature-line">ผู้รับเงิน</div>
            </div>
        </div>
    </div>
</body>
</html>
