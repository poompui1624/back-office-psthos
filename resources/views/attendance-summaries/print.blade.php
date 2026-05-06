<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>รายงานสรุปเวลาทำงาน</title>

    @include('print._document-style')

    <style>
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-normal {
            background: #dcfce7;
            color: #166534;
        }

        .status-late {
            background: #fef9c3;
            color: #854d0e;
        }

        .status-early {
            background: #ffedd5;
            color: #9a3412;
        }

        .status-bad {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-gray {
            background: #f3f4f6;
            color: #374151;
        }

        table {
            font-size: 12px;
        }

        th,
        td {
            padding: 6px;
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">พิมพ์</button>
    </div>

    <div class="page">
        @include('print._document-header')

        <div class="document-title">
            รายงานสรุปเวลาทำงาน
        </div>

        @php
            $statusLabels = [
                'normal' => 'ปกติ',
                'late' => 'มาสาย',
                'early_leave' => 'กลับก่อน',
                'late_and_early_leave' => 'สายและกลับก่อน',
                'incomplete' => 'ข้อมูลไม่ครบ',
                'absent' => 'ไม่พบสแกน',
                'off' => 'วันหยุด',
            ];

            $totalLateMinutes = $summaries->sum('late_minutes');
            $totalEarlyLeaveMinutes = $summaries->sum('early_leave_minutes');

            $statusClass = function ($status) {
                return match ($status) {
                    'normal' => 'status-normal',
                    'late' => 'status-late',
                    'early_leave' => 'status-early',
                    'late_and_early_leave', 'absent' => 'status-bad',
                    default => 'status-gray',
                };
            };
        @endphp

        <div class="section">
            <div class="section-title">เงื่อนไขรายงาน</div>

            <div class="info-grid">
                <div>
                    <div class="label">วันที่เริ่ม</div>
                    <div class="value">{{ $dateFrom ?: '-' }}</div>
                </div>

                <div>
                    <div class="label">วันที่สิ้นสุด</div>
                    <div class="value">{{ $dateTo ?: '-' }}</div>
                </div>

                <div>
                    <div class="label">คำค้นหา</div>
                    <div class="value">{{ $search ?: '-' }}</div>
                </div>

                <div>
                    <div class="label">สถานะ</div>
                    <div class="value">{{ $statusLabels[$status] ?? ($status ?: 'ทุกสถานะ') }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">สรุปภาพรวม</div>

            <div class="info-grid">
                <div>
                    <div class="label">จำนวนรายการ</div>
                    <div class="value">{{ $summaries->count() }} รายการ</div>
                </div>

                <div>
                    <div class="label">รวมเวลามาสาย</div>
                    <div class="value">{{ $totalLateMinutes }} นาที</div>
                </div>

                <div>
                    <div class="label">รวมเวลากลับก่อน</div>
                    <div class="value">{{ $totalEarlyLeaveMinutes }} นาที</div>
                </div>

                <div>
                    <div class="label">พิมพ์เมื่อ</div>
                    <div class="value">{{ now()->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">รายละเอียดเวลาทำงาน</div>

            <table>
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">วันที่</th>
                        <th>บุคลากร</th>
                        <th>หน่วยงาน</th>
                        <th>เวร</th>
                        <th class="text-center">เข้า</th>
                        <th class="text-center">ออก</th>
                        <th class="text-center">ทำงาน</th>
                        <th class="text-center">สาย</th>
                        <th class="text-center">กลับก่อน</th>
                        <th class="text-center">สถานะ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($summaries as $summary)
                        <tr>
                            <td class="text-center">
                                {{ $summary->work_date?->format('Y-m-d') }}
                            </td>

                            <td>
                                {{ $summary->employee?->employee_code }}
                                <br>
                                {{ $summary->employee?->full_name ?? '-' }}
                            </td>

                            <td>
                                {{ $summary->employee?->department?->name ?? '-' }}
                            </td>

                            <td>
                                @if ($summary->dutySchedule)
                                    {{ $summary->dutySchedule->shiftType?->name ?? '-' }}
                                    <br>
                                    {{ $summary->dutySchedule->start_at?->format('H:i') }}
                                    -
                                    {{ $summary->dutySchedule->end_at?->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="text-center">
                                {{ $summary->first_in_at?->format('H:i') ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ $summary->last_out_at?->format('H:i') ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ $summary->work_hours }}
                            </td>

                            <td class="text-center">
                                {{ $summary->late_minutes > 0 ? $summary->late_minutes . ' นาที' : '-' }}
                            </td>

                            <td class="text-center">
                                {{ $summary->early_leave_minutes > 0 ? $summary->early_leave_minutes . ' นาที' : '-' }}
                            </td>

                            <td class="text-center">
                                <span class="status {{ $statusClass($summary->status) }}">
                                    {{ $statusLabels[$summary->status] ?? $summary->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                ไม่พบข้อมูล
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="signature-grid">
            <div>
                <div class="signature-line">ผู้จัดทำรายงาน</div>
            </div>

            <div>
                <div class="signature-line">ผู้ตรวจสอบ</div>
            </div>
        </div>
    </div>
</body>
</html>
