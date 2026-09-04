<x-layouts.app title="รายละเอียดโมดูลการลา">
    @php
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม',
        ];

        $statusCards = [
            ['label' => 'รออนุมัติ', 'value' => $summary['pending'], 'route' => route('leave-requests.index', ['status' => 'pending']), 'class' => 'from-amber-400 to-orange-500'],
            ['label' => 'อนุมัติแล้ว', 'value' => $summary['approved'], 'route' => route('leave-requests.index', ['status' => 'approved']), 'class' => 'from-emerald-400 to-green-500'],
            ['label' => 'ไม่อนุมัติ', 'value' => $summary['rejected'], 'route' => route('leave-requests.index', ['status' => 'rejected']), 'class' => 'from-rose-400 to-red-500'],
            ['label' => 'ยกเลิก', 'value' => $summary['cancelled'], 'route' => route('leave-requests.index', ['status' => 'cancelled']), 'class' => 'from-slate-500 to-slate-700'],
        ];

        $statusBadgeClasses = [
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-100',
            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
            'rejected' => 'bg-rose-50 text-rose-700 ring-rose-100',
            'cancelled' => 'bg-slate-100 text-slate-600 ring-slate-200',
        ];

        $maxLeaveTypeDays = max((float) collect($leaveTypeStats)->max('total_days'), 1);
        $maxDepartmentDays = max((float) $departmentStats->max('total_days'), 1);
        $selectedDepartment = $departments->firstWhere('id', (int) $departmentId)?->name ?? 'ทุกหน่วยงาน';
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-[#02abff] to-blue-600 text-white shadow-lg shadow-sky-100">
            <div class="grid gap-6 px-5 py-6 lg:grid-cols-[1fr_auto] lg:items-center lg:px-8">
                <div>
                    <div class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">
                        ระบบการลา
                    </div>
                    <h1 class="mt-3 text-2xl font-bold tracking-normal sm:text-3xl">
                        รายละเอียดโมดูลการลา
                    </h1>
                    <p class="mt-2 max-w-3xl text-sm text-sky-50">
                        ภาพรวมคำขอลา อนุมัติ วันลา และรายงานประจำเดือน {{ $thaiMonths[$month] }} {{ $year }} สำหรับ {{ $selectedDepartment }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('leave-requests.index') }}"
                       class="rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">
                        รายการลา
                    </a>
                    <a href="{{ route('leave-requests.calendar') }}"
                       class="rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">
                        ปฏิทินลา
                    </a>
                    @can('leave.create')
                        <a href="{{ route('leave-requests.create') }}"
                           class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-50">
                            ยื่นคำขอลา
                        </a>
                    @endcan
                    <a href="{{ route('exports.table', 'leave_requests') }}"
                       class="rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">
                        Excel
                    </a>
                    <button type="button"
                            onclick="window.print()"
                            class="rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">
                        พิมพ์
                    </button>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('leave-requests.dashboard') }}" class="grid gap-3 md:grid-cols-5">
                <label>
                    <span class="mb-1 block text-xs font-semibold text-slate-500">เดือน</span>
                    <select name="month" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#02abff] focus:ring-[#02abff]">
                        @foreach ($thaiMonths as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected($month === $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="mb-1 block text-xs font-semibold text-slate-500">ปี พ.ศ.</span>
                    <input type="number"
                           name="year"
                           value="{{ $year }}"
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-[#02abff] focus:ring-[#02abff]">
                </label>

                <label class="md:col-span-2">
                    <span class="mb-1 block text-xs font-semibold text-slate-500">หน่วยงาน</span>
                    <select name="department_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#02abff] focus:ring-[#02abff]">
                        <option value="">ทุกหน่วยงาน</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected($departmentId == $department->id)>
                                {{ $department->code }} - {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="rounded-xl bg-[#02abff] px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-sky-100 transition hover:bg-sky-600">
                        แสดงผล
                    </button>
                    <a href="{{ route('leave-requests.dashboard') }}"
                       class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        ล้าง
                    </a>
                </div>
            </form>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
            <a href="{{ route('leave-requests.calendar', ['month' => $month, 'year' => $year - 543]) }}"
               class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold text-slate-500">รายการเดือนนี้</p>
                <p class="mt-3 text-3xl font-bold text-[#02abff]">{{ number_format($summary['this_month']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $thaiMonths[$month] }} {{ $year }}</p>
            </a>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold text-slate-500">วันลาที่อนุมัติ</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($summary['approved_days'], 1) }}</p>
                <p class="mt-1 text-xs text-slate-500">รวมจำนวนวันลา</p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold text-slate-500">วันนี้มีคนลา</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($summary['today_on_leave']) }}</p>
                <p class="mt-1 text-xs text-slate-500">เฉพาะรายการอนุมัติแล้ว</p>
            </div>

            @foreach ($statusCards as $card)
                <a href="{{ $card['route'] }}"
                   class="rounded-2xl bg-gradient-to-br {{ $card['class'] }} p-5 text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <p class="text-xs font-semibold text-white/80">{{ $card['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold">{{ number_format($card['value']) }}</p>
                    <p class="mt-1 text-xs text-white/80">คำขอ</p>
                </a>
            @endforeach
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">สรุปประเภทการลา</h2>
                    <span class="text-xs font-medium text-slate-500">นับตามเดือนที่เลือก</span>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    @forelse ($leaveTypeStats as $leaveType)
                        @php
                            $width = max(4, ($leaveType['total_days'] / $maxLeaveTypeDays) * 100);
                        @endphp
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $leaveType['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $leaveType['code'] }}</p>
                                </div>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                    {{ number_format($leaveType['total_days'], 1) }} วัน
                                </span>
                            </div>

                            <div class="mt-4 h-2 rounded-full bg-white">
                                <div class="h-2 rounded-full bg-[#02abff]" style="width: {{ $width }}%"></div>
                            </div>

                            <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                                <span>{{ number_format($leaveType['total_requests']) }} คำขอ</span>
                                <span>{{ $leaveType['requires_document'] ? 'ต้องแนบเอกสาร' : 'ไม่บังคับเอกสาร' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center sm:col-span-2">
                            <p class="font-semibold text-slate-700">ยังไม่มีประเภทการลา</p>
                            <p class="mt-1 text-sm text-slate-500">เพิ่มประเภทการลาเพื่อเริ่มเก็บสถิติ</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">สรุปตามหน่วยงาน</h2>
                    <span class="text-xs font-medium text-slate-500">Top 8</span>
                </div>

                <div class="space-y-3">
                    @forelse ($departmentStats as $department)
                        @php
                            $width = max(6, ((float) $department->total_days / $maxDepartmentDays) * 100);
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                                <span class="truncate font-semibold text-slate-700">{{ $department->department_name }}</span>
                                <span class="text-xs font-semibold text-slate-500">{{ number_format((float) $department->total_days, 1) }} วัน</span>
                            </div>
                            <div class="h-3 rounded-full bg-slate-100">
                                <div class="h-3 rounded-full bg-gradient-to-r from-[#02abff] to-blue-500" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                            <p class="font-semibold text-slate-700">ยังไม่มีข้อมูลให้สรุป</p>
                            <p class="mt-1 text-sm text-slate-500">เมื่อมีรายการลา ระบบจะแสดงกราฟหน่วยงานให้ทันที</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">ลาวันนี้</h2>
                    <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">{{ $todayLeaves->count() }} รายการ</span>
                </div>
                <div class="space-y-3">
                    @forelse ($todayLeaves as $leave)
                        <a href="{{ route('leave-requests.show', $leave) }}"
                           class="block rounded-2xl border border-slate-100 p-4 transition hover:bg-sky-50/60">
                            <div class="font-semibold text-slate-800">{{ $leave->employee?->full_name ?? '-' }}</div>
                            <div class="mt-1 text-sm text-slate-500">{{ $leave->leaveType?->name ?? '-' }} | {{ $leave->department?->name ?? '-' }}</div>
                            <div class="mt-2 text-xs text-slate-400">{{ $leave->start_date?->format('d/m/Y') }} - {{ $leave->end_date?->format('d/m/Y') }}</div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                            วันนี้ไม่มีรายการลา
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">รออนุมัติล่าสุด</h2>
                    <a href="{{ route('leave-requests.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-[#02abff] hover:text-sky-700">ดูทั้งหมด</a>
                </div>
                <div class="space-y-3">
                    @forelse ($pendingRequests as $leave)
                        <a href="{{ route('leave-requests.show', $leave) }}"
                           class="block rounded-2xl border border-slate-100 p-4 transition hover:bg-sky-50/60">
                            <div class="font-semibold text-slate-800">{{ $leave->request_no }}</div>
                            <div class="mt-1 text-sm text-slate-600">{{ $leave->employee?->full_name ?? '-' }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ $leave->leaveType?->name ?? '-' }} / {{ number_format((float) $leave->total_days, 1) }} วัน</div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                            ไม่มีคำขอรออนุมัติ
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">รายการลาถัดไป</h2>
                    <a href="{{ route('leave-requests.calendar') }}" class="text-sm font-semibold text-[#02abff] hover:text-sky-700">ดูปฏิทิน</a>
                </div>
                <div class="space-y-3">
                    @forelse ($upcomingLeaves as $leave)
                        <a href="{{ route('leave-requests.show', $leave) }}"
                           class="block rounded-2xl border border-slate-100 p-4 transition hover:bg-sky-50/60">
                            <div class="font-semibold text-slate-800">{{ $leave->employee?->full_name ?? '-' }}</div>
                            <div class="mt-1 text-sm text-slate-500">{{ $leave->leaveType?->name ?? '-' }} | {{ $leave->department?->name ?? '-' }}</div>
                            <div class="mt-2 text-xs text-slate-400">{{ $leave->start_date?->format('d/m/Y') }} - {{ $leave->end_date?->format('d/m/Y') }}</div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                            ไม่มีรายการลาถัดไป
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-[#02abff] ring-1 ring-sky-100">
                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M7 3v3M17 3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
                            <path d="M8 13h2M14 13h2M8 17h2M14 17h2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">รายละเอียดโมดูลการลา</h2>
                        <p class="mt-2 text-sm text-slate-500">
                            รองรับการยื่นคำขอ ตรวจสอบ อนุมัติ รายงาน และสรุปข้อมูลการลาของบุคลากรทั้งองค์กร
                        </p>
                    </div>
                </div>

                <ul class="mt-6 space-y-2 text-sm text-slate-600">
                    <li>• ผู้ดูแลสามารถเพิ่มประเภทการลาและกำหนดเงื่อนไขเอกสารแนบได้</li>
                    <li>• บันทึกคำขอลา ตรวจสอบ และอนุมัติหรือไม่อนุมัติได้จากระบบ</li>
                    <li>• แสดงรายงานข้อมูลการลารายบุคคลและรายหน่วยงาน</li>
                    <li>• ส่งออกข้อมูลเป็นไฟล์ Excel ได้</li>
                    <li>• คำนวณจำนวนวันลา และแสดงรายการลาวันนี้/ลาถัดไป</li>
                    <li>• มีปฏิทินการลาเพื่อดูภาพรวมตามเดือน</li>
                    <li>• มีสถานะคำขอ รออนุมัติ อนุมัติแล้ว ไม่อนุมัติ และยกเลิก</li>
                </ul>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-bold text-slate-900">รายการล่าสุดในเดือนที่เลือก</h2>
                    <p class="mt-1 text-sm text-slate-500">ใช้ตรวจสอบภาพรวมก่อนเข้าไปดูรายละเอียดแต่ละคำขอ</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold">เลขคำขอ</th>
                                <th class="px-5 py-3 text-left font-semibold">บุคลากร</th>
                                <th class="px-5 py-3 text-left font-semibold">ประเภท</th>
                                <th class="px-5 py-3 text-center font-semibold">วันลา</th>
                                <th class="px-5 py-3 text-center font-semibold">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentRequests as $leave)
                                <tr class="transition hover:bg-sky-50/50">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('leave-requests.show', $leave) }}" class="font-semibold text-[#02abff] hover:text-sky-700">
                                            {{ $leave->request_no }}
                                        </a>
                                        <div class="text-xs text-slate-400">{{ $leave->created_at?->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-800">{{ $leave->employee?->full_name ?? '-' }}</div>
                                        <div class="text-xs text-slate-500">{{ $leave->department?->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $leave->leaveType?->name ?? '-' }}</td>
                                    <td class="px-5 py-4 text-center text-slate-600">{{ number_format((float) $leave->total_days, 1) }}</td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusBadgeClasses[$leave->status] ?? 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                            {{ ['pending' => 'รออนุมัติ', 'approved' => 'อนุมัติแล้ว', 'rejected' => 'ไม่อนุมัติ', 'cancelled' => 'ยกเลิก'][$leave->status] ?? $leave->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center">
                                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8">
                                            <p class="font-semibold text-slate-700">ยังไม่มีรายการลาในเดือนนี้</p>
                                            <p class="mt-1 text-sm text-slate-500">เมื่อมีคำขอ ระบบจะแสดงรายการล่าสุดตรงนี้</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
