<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>ใบจองห้องประชุม {{ $meetingBooking->booking_no }}</title>

    @include('print._document-style')
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">พิมพ์</button>
    </div>

    <div class="page">
        @include('print._document-header')

        <div class="document-title">
            ใบจองห้องประชุม
        </div>

        @php
            $statusLabels = [
                'pending' => 'รออนุมัติ',
                'approved' => 'อนุมัติแล้ว',
                'rejected' => 'ไม่อนุมัติ',
                'cancelled' => 'ยกเลิก',
            ];

            $equipment = [];

            if ($meetingBooking->need_projector) {
                $equipment[] = 'โปรเจคเตอร์';
            }

            if ($meetingBooking->need_sound_system) {
                $equipment[] = 'เครื่องเสียง';
            }

            if ($meetingBooking->need_video_conference) {
                $equipment[] = 'Video Conference';
            }

            if ($meetingBooking->need_whiteboard) {
                $equipment[] = 'Whiteboard';
            }
        @endphp

        <div class="section">
            <div class="section-title">ข้อมูลการจอง</div>

            <div class="info-grid">
                <div>
                    <div class="label">เลขที่จอง</div>
                    <div class="value">{{ $meetingBooking->booking_no }}</div>
                </div>

                <div>
                    <div class="label">สถานะ</div>
                    <div class="value">
                        {{ $statusLabels[$meetingBooking->status] ?? $meetingBooking->status }}
                    </div>
                </div>

                <div>
                    <div class="label">หัวข้อการประชุม</div>
                    <div class="value">{{ $meetingBooking->title }}</div>
                </div>

                <div>
                    <div class="label">จำนวนผู้เข้าร่วม</div>
                    <div class="value">{{ $meetingBooking->attendees_count }} คน</div>
                </div>

                <div>
                    <div class="label">ห้องประชุม</div>
                    <div class="value">
                        {{ $meetingBooking->room?->name ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="label">สถานที่</div>
                    <div class="value">
                        {{ $meetingBooking->room?->location ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="label">เริ่มประชุม</div>
                    <div class="value">
                        {{ $meetingBooking->start_at?->format('Y-m-d H:i') }}
                    </div>
                </div>

                <div>
                    <div class="label">สิ้นสุดประชุม</div>
                    <div class="value">
                        {{ $meetingBooking->end_at?->format('Y-m-d H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">ผู้จอง</div>

            <div class="info-grid">
                <div>
                    <div class="label">ชื่อผู้จอง</div>
                    <div class="value">
                        {{ $meetingBooking->employee?->full_name ?? $meetingBooking->creator?->name ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="label">หน่วยงาน</div>
                    <div class="value">
                        {{ $meetingBooking->department?->name ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">รายละเอียดเพิ่มเติม</div>

            <table>
                <tbody>
                    <tr>
                        <th style="width: 180px;">วัตถุประสงค์</th>
                        <td>{{ $meetingBooking->purpose ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>อุปกรณ์ที่ต้องการ</th>
                        <td>
                            {{ count($equipment) > 0 ? implode(', ', $equipment) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>หมายเหตุ</th>
                        <td>{{ $meetingBooking->remark ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">ผลการอนุมัติ</div>

            <div class="info-grid">
                <div>
                    <div class="label">ผู้อนุมัติ</div>
                    <div class="value">{{ $meetingBooking->approver?->name ?? '-' }}</div>
                </div>

                <div>
                    <div class="label">วันที่อนุมัติ</div>
                    <div class="value">
                        {{ $meetingBooking->approved_at?->format('Y-m-d H:i') ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="label">ผู้ไม่อนุมัติ</div>
                    <div class="value">{{ $meetingBooking->rejecter?->name ?? '-' }}</div>
                </div>

                <div>
                    <div class="label">วันที่ไม่อนุมัติ</div>
                    <div class="value">
                        {{ $meetingBooking->rejected_at?->format('Y-m-d H:i') ?? '-' }}
                    </div>
                </div>

                <div style="grid-column: span 2;">
                    <div class="label">หมายเหตุการอนุมัติ</div>
                    <div class="value">
                        {{ $meetingBooking->approval_remark ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="signature-grid">
            <div>
                <div class="signature-line">ผู้จอง</div>
            </div>

            <div>
                <div class="signature-line">ผู้อนุมัติ</div>
            </div>
        </div>
    </div>
</body>
</html>
