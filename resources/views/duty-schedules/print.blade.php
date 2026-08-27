<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>ตารางเวรประจำเดือน</title>

    @include('print._document-style')

    <style>
        @page { size: A4 landscape; }

        .roster {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            table-layout: fixed;
        }

        .roster th,
        .roster td {
            border: 1px solid #cbd5e1;
            padding: 3px 2px;
            text-align: center;
            word-break: break-word;
        }

        .roster th { background: #f1f5f9; font-weight: bold; }

        .roster .name-col {
            width: 150px;
            text-align: left;
            padding-left: 6px;
        }

        .roster .weekend { background: #fef2f2; }
        .roster td.filled { font-weight: bold; }

        .legend {
            margin-top: 12px;
            font-size: 11px;
        }

        .legend span {
            display: inline-block;
            margin-right: 14px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="document">
        @include('print._document-header')

        <h2 style="text-align:center; margin: 8px 0 4px;">
            ตารางเวรประจำเดือน {{ $selectedMonth->translatedFormat('F') }} {{ $year }}
        </h2>

        <div style="text-align:center; font-size:12px; margin-bottom:10px;">
            @if ($department)
                หน่วยงาน: {{ $department->name }}
            @else
                ทุกหน่วยงาน
            @endif

            @if ($roleGroup)
                &middot; กลุ่มงาน: {{ $roleGroup }}
            @endif
        </div>

        @if ($employees->isEmpty())
            <p style="text-align:center; padding: 30px 0;">ยังไม่มีการจัดเวรในเดือนนี้</p>
        @else
            <table class="roster">
                <thead>
                    <tr>
                        <th class="name-col">บุคลากร</th>

                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = $selectedMonth->copy()->day($day);
                                $isWeekend = $date->isWeekend();
                            @endphp

                            <th class="{{ $isWeekend ? 'weekend' : '' }}">{{ $day }}</th>
                        @endfor

                        <th style="width:34px;">รวม</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($employees as $employee)
                        @php $row = $grid->get($employee->id, collect()); @endphp

                        <tr>
                            <td class="name-col">
                                {{ $employee->employee_code }}<br>{{ $employee->full_name }}
                            </td>

                            @for ($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $codes = $row->get($day, []);
                                    $isWeekend = $selectedMonth->copy()->day($day)->isWeekend();
                                @endphp

                                <td class="{{ $isWeekend ? 'weekend' : '' }} {{ $codes ? 'filled' : '' }}">
                                    {{ implode('/', $codes) }}
                                </td>
                            @endfor

                            <td style="font-weight:bold;">
                                {{ $row->flatten()->count() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="legend">
                <strong>คำอธิบายรหัสเวร:</strong>
                @foreach ($shiftLegend as $shift)
                    <span>
                        {{ $shift->code }} = {{ $shift->name }}
                        @if ($shift->is_ot) (OT) @endif
                    </span>
                @endforeach
            </div>
        @endif

        <div class="signature-grid">
            <div>
                <div class="signature-line">ผู้จัดตารางเวร</div>
            </div>

            <div>
                <div class="signature-line">หัวหน้าหน่วยงาน</div>
            </div>
        </div>
    </div>
</body>
</html>
