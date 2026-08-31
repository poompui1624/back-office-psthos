<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>สลิปเงินเดือน {{ $payslip->employee?->employee_code }}</title>

    {{--
        Shared print styles first, then the payslip's own overrides. This file
        used to declare its own full copy of the shared rules above the include,
        so both were loaded and the later one silently won.
    --}}
    @include('print._document-style')

    <style>
        /* Tuned so a payslip lands on a single A4 sheet. */
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            font-size: 13px;
        }

        .page {
            padding: 16px;
        }

        .document-header {
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .document-logo {
            margin-bottom: 4px;
        }

        .document-logo img {
            height: 62px;
            max-width: 115px;
        }

        .document-hospital-name {
            font-size: 19px;
        }

        .document-hospital-info {
            margin-top: 3px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .title {
            font-size: 19px;
            font-weight: bold;
        }

        .subtitle {
            margin-top: 3px;
            color: #4b5563;
            font-size: 12px;
        }

        .info-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 9px 20px;
            margin-bottom: 16px;
        }

        .label {
            font-size: 11px;
        }

        .value {
            font-size: 13px;
            margin-top: 2px;
        }

        /* The two item tables sit side by side; stacked they were what pushed
           the signatures onto a second sheet. */
        .items {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        table {
            margin-bottom: 0;
        }

        th,
        td {
            padding: 6px 8px;
            font-size: 12px;
        }

        .item-note {
            font-size: 10px;
            color: #6b7280;
        }

        .summary {
            border: 2px solid #111827;
            padding: 12px 16px;
            text-align: right;
        }

        .net {
            font-size: 22px;
            font-weight: bold;
        }

        .signature-grid {
            margin-top: 52px;
            gap: 56px;
        }

        .signature-line {
            padding-top: 8px;
            font-size: 12px;
        }

        @media print {
            .page {
                width: auto;
                padding: 0;
            }

            /* Never split a table or the totals box across sheets. */
            .items,
            table,
            .summary,
            .signature-grid {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
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
            @foreach ([
                'รหัสบุคลากร' => $payslip->employee?->employee_code,
                'ชื่อ - สกุล' => $payslip->employee?->full_name,
                'หน่วยงาน' => $payslip->employee?->department?->name,
                'ตำแหน่ง' => $payslip->employee?->position?->name,
                'มาสาย' => $payslip->late_minutes . ' นาที',
                'กลับก่อน' => $payslip->early_leave_minutes . ' นาที',
                'ขาดงาน / ไม่พบสแกน' => $payslip->absent_days . ' วัน',
                'วันที่สร้างสลิป' => $payslip->generated_at?->format('Y-m-d H:i'),
            ] as $label => $value)
                <div>
                    <div class="label">{{ $label }}</div>
                    <div class="value">{{ $value ?? '-' }}</div>
                </div>
            @endforeach
        </div>

        @php
            $groups = [
                [
                    'heading' => 'รายได้',
                    'items' => $payslip->items->where('type', 'income')->sortBy('sort_order'),
                    'totalLabel' => 'รวมรายได้',
                    'total' => $payslip->gross_income,
                ],
                [
                    'heading' => 'รายการหัก',
                    'items' => $payslip->items->where('type', 'deduction')->sortBy('sort_order'),
                    'totalLabel' => 'รวมรายการหัก',
                    'total' => $payslip->total_deduction,
                ],
            ];
        @endphp

        <div class="items">
            @foreach ($groups as $group)
                <table>
                    <thead>
                        <tr>
                            <th colspan="2">{{ $group['heading'] }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($group['items'] as $item)
                            <tr>
                                <td>
                                    {{ $item->name }}

                                    @if ($item->quantity > 1)
                                        <div class="item-note">
                                            {{ $item->quantity }} x {{ number_format($item->unit_amount, 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">ไม่มีรายการ</td>
                            </tr>
                        @endforelse

                        <tr>
                            <td><strong>{{ $group['totalLabel'] }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($group['total'], 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        </div>

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
