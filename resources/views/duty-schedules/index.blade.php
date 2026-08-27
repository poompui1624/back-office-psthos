<x-layouts.app title="ระบบจัดตารางเวร">
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

        $statusLabels = [
            'assigned' => 'จัดแล้ว',
            'confirmed' => 'ยืนยันแล้ว',
            'cancelled' => 'ยกเลิก',
        ];

        $statusClasses = [
            'assigned' => 'bg-sky-50 text-sky-700 ring-sky-100',
            'confirmed' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
            'cancelled' => 'bg-slate-100 text-slate-600 ring-slate-200',
        ];

        $departmentName = $departments->firstWhere('id', (int) $departmentId)?->name ?? 'ทุกหน่วยงาน';
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-[#02abff] to-blue-600 text-white shadow-lg shadow-sky-100">
            <div class="flex flex-col gap-6 px-5 py-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div>
                    <div class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">
                        พร้อมใช้งาน
                    </div>
                    <h1 class="mt-3 text-2xl font-bold tracking-normal sm:text-3xl">
                        ระบบจัดตารางเวรทุกหน่วยงาน
                    </h1>
                    <p class="mt-2 max-w-3xl text-sm text-sky-50">
                        ภาพรวมเวรประจำเดือน {{ $thaiMonths[$month] }} {{ $year }} สำหรับ {{ $departmentName }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('duty-schedules.calendar', ['month' => $month, 'year' => $year - 543, 'department_id' => $departmentId, 'role_group' => $roleGroup]) }}"
                       class="rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">
                        ตารางเวร
                    </a>

                    @can('duty.create')
                        <a href="{{ route('duty-schedules.bulk-create') }}"
                           class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-sky-700 shadow-sm transition hover:bg-sky-50">
                            จัดเวรหลายรายการ
                        </a>
                        <a href="{{ route('duty-schedules.create') }}"
                           class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                            เพิ่มเวร
                        </a>
                    @endcan

                    <a href="{{ route('exports.table', 'duty_schedules') }}"
                       class="rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">
                        Excel
                    </a>

                    <a href="{{ route('duty-schedules.print', ['month' => $month, 'year' => $year, 'department_id' => $departmentId, 'role_group' => $roleGroup]) }}"
                       target="_blank"
                       class="rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">
                        พิมพ์ตารางเวร
                    </a>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('duty_warnings'))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 shadow-sm">
                <div class="text-sm font-semibold text-amber-900">
                    บันทึกแล้ว แต่มีข้อควรตรวจสอบ {{ count(session('duty_warnings')) }} รายการ
                </div>

                <ul class="mt-2 space-y-1 text-sm text-amber-800">
                    @foreach (array_slice(session('duty_warnings'), 0, 20) as $warning)
                        <li>&bull; {{ $warning }}</li>
                    @endforeach
                </ul>

                @if (count(session('duty_warnings')) > 20)
                    <div class="mt-2 text-xs text-amber-700">
                        และอีก {{ count(session('duty_warnings')) - 20 }} รายการ
                    </div>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-100 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-800 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('duty-schedules.index') }}" class="grid gap-3 lg:grid-cols-12">
                <label class="lg:col-span-2">
                    <span class="mb-1 block text-xs font-semibold text-slate-500">เดือน</span>
                    <select name="month" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#02abff] focus:ring-[#02abff]">
                        @foreach ($thaiMonths as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected($month === $monthNumber)>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="lg:col-span-2">
                    <span class="mb-1 block text-xs font-semibold text-slate-500">ปี พ.ศ.</span>
                    <input type="number"
                           name="year"
                           value="{{ $year }}"
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-[#02abff] focus:ring-[#02abff]">
                </label>

                <label class="lg:col-span-3">
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

                <label class="lg:col-span-2">
                    <span class="mb-1 block text-xs font-semibold text-slate-500">กลุ่มงาน</span>
                    <input type="text"
                           name="role_group"
                           value="{{ $roleGroup }}"
                           placeholder="เช่น พยาบาล"
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-[#02abff] focus:ring-[#02abff]">
                </label>

                <label class="lg:col-span-2">
                    <span class="mb-1 block text-xs font-semibold text-slate-500">ค้นหาบุคลากร</span>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="รหัส / ชื่อ"
                           class="w-full rounded-xl border-slate-200 text-sm focus:border-[#02abff] focus:ring-[#02abff]">
                </label>

                <label class="lg:col-span-1">
                    <span class="mb-1 block text-xs font-semibold text-slate-500">แสดง</span>
                    <select name="per_page" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#02abff] focus:ring-[#02abff]">
                        <option value="10" @selected($perPage == 10)>10</option>
                        <option value="25" @selected($perPage == 25)>25</option>
                        <option value="50" @selected($perPage == 50)>50</option>
                        <option value="100" @selected($perPage == 100)>100</option>
                    </select>
                </label>

                <div class="flex flex-wrap items-end gap-2 lg:col-span-12">
                    <button type="submit"
                            class="rounded-xl bg-[#02abff] px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-sky-100 transition hover:bg-sky-600 disabled:cursor-wait disabled:opacity-60">
                        แสดงผล
                    </button>
                    <a href="{{ route('duty-schedules.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        ล้าง
                    </a>
                </div>
            </form>
        </section>

        <nav class="flex gap-2 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
            <a href="{{ route('duty-schedules.index', request()->query()) }}"
               class="whitespace-nowrap rounded-xl bg-[#02abff] px-4 py-2 text-sm font-semibold text-white shadow-sm">
                ภาพรวม
            </a>
            <a href="{{ route('duty-schedules.calendar', ['month' => $month, 'year' => $year - 543, 'department_id' => $departmentId, 'role_group' => $roleGroup]) }}"
               class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                ตารางเวร
            </a>
            <a href="{{ route('leave-requests.calendar') }}"
               class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                วันลา
            </a>
            <a href="{{ route('shift-types.index') }}"
               class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                ประเภทเวร
            </a>
            @can('employee.view')
                <a href="{{ route('employees.index') }}"
                   class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    บุคลากร
                </a>
            @endcan
        </nav>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold text-slate-500">วันในเดือน</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($dashboard['days_in_month']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $thaiMonths[$month] }} {{ $year }}</p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold text-slate-500">บุคลากรที่ใช้งาน</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($dashboard['employee_count']) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $departmentName }}</p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold text-slate-500">เวรที่จัดแล้ว</p>
                <p class="mt-3 text-3xl font-bold text-[#02abff]">{{ number_format($dashboard['assigned_count']) }}</p>
                <p class="mt-1 text-xs text-slate-500">ยืนยัน {{ number_format($dashboard['confirmed_count']) }} รายการ</p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold text-slate-500">วันลาอนุมัติ</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($dashboard['leave_days'], 1) }}</p>
                <p class="mt-1 text-xs text-slate-500">รวมรายการที่ทับเดือนนี้</p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold text-slate-500">OT ในเดือน</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($dashboard['ot_count']) }}</p>
                <p class="mt-1 text-xs text-slate-500">นับจากประเภทเวร OT</p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-semibold text-slate-500">ยกเลิก</p>
                <p class="mt-3 text-3xl font-bold text-rose-600">{{ number_format($dashboard['cancelled_count']) }}</p>
                <p class="mt-1 text-xs text-slate-500">ควรตรวจเวรซ้ำ</p>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">การกระจายประเภทเวร</h2>
                    <span class="text-xs font-medium text-slate-500">รวม {{ number_format($dashboard['assigned_count']) }} เวร</span>
                </div>

                <div class="space-y-3">
                    @forelse ($dashboard['shift_distribution'] as $shift)
                        @php
                            $width = max(6, ((int) $shift->total / $dashboard['max_shift_count']) * 100);
                        @endphp
                        <div class="grid grid-cols-[5rem_1fr_4rem] items-center gap-3 text-sm">
                            <div class="truncate font-semibold text-slate-700">{{ $shift->code }}</div>
                            <div class="h-8 overflow-hidden rounded-lg bg-slate-100">
                                <div class="flex h-full items-center rounded-lg bg-gradient-to-r from-[#02abff] to-blue-500 px-3 text-xs font-semibold text-white"
                                     style="width: {{ $width }}%">
                                    {{ $shift->name }}
                                </div>
                            </div>
                            <div class="text-right font-semibold text-slate-700">{{ number_format($shift->total) }} เวร</div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                            <p class="font-semibold text-slate-700">ยังไม่มีข้อมูลเวรในเดือนนี้</p>
                            <p class="mt-1 text-sm text-slate-500">เริ่มจากสร้างเวรหลายรายการหรือเพิ่มเวรรายบุคคล</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">ภาระงาน Top 10</h2>
                    <span class="text-xs font-medium text-slate-500">เรียงจากมากไปน้อย</span>
                </div>

                <div class="space-y-3">
                    @forelse ($dashboard['workload_top'] as $index => $employee)
                        @php
                            $employeeName = trim(($employee->prefix ? $employee->prefix . ' ' : '') . $employee->first_name . ' ' . $employee->last_name);
                            $width = max(8, ((int) $employee->total / $dashboard['max_workload_count']) * 100);
                        @endphp
                        <div class="grid grid-cols-[2rem_1fr_4rem] items-center gap-3 text-sm">
                            <div class="text-right text-slate-400">{{ $index + 1 }}.</div>
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="truncate font-semibold text-slate-700">{{ $employeeName }}</span>
                                    <span class="text-xs text-slate-400">{{ $employee->employee_code }}</span>
                                </div>
                                <div class="mt-1 h-2 rounded-full bg-slate-100">
                                    <div class="h-2 rounded-full bg-[#02abff]" style="width: {{ $width }}%"></div>
                                </div>
                            </div>
                            <div class="text-right font-semibold text-slate-700">{{ number_format($employee->total) }}</div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                            <p class="font-semibold text-slate-700">ยังไม่มีภาระงานให้สรุป</p>
                            <p class="mt-1 text-sm text-slate-500">เมื่อมีตารางเวร ระบบจะแสดง Top workload ให้อัตโนมัติ</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">รายการเวรประจำเดือน</h2>
                    <p class="mt-1 text-sm text-slate-500">ใช้สำหรับตรวจสอบ แก้ไข หรือลบรายการเวรที่จัดไว้แล้ว</p>
                </div>
                @can('duty.create')
                    <a href="{{ route('duty-schedules.create') }}"
                       class="inline-flex justify-center rounded-xl bg-[#02abff] px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600">
                        เพิ่มเวร
                    </a>
                @endcan
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[920px] border-collapse text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold">วันที่</th>
                            <th class="px-5 py-3 text-left font-semibold">บุคลากร</th>
                            <th class="px-5 py-3 text-left font-semibold">หน่วยงาน</th>
                            <th class="px-5 py-3 text-left font-semibold">เวร</th>
                            <th class="px-5 py-3 text-center font-semibold">เวลา</th>
                            <th class="px-5 py-3 text-left font-semibold">กลุ่มงาน</th>
                            <th class="px-5 py-3 text-center font-semibold">สถานะ</th>
                            <th class="px-5 py-3 text-right font-semibold">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($schedules as $schedule)
                            <tr class="transition hover:bg-sky-50/50">
                                <td class="px-5 py-4 whitespace-nowrap font-semibold text-slate-700">
                                    {{ $schedule->work_date?->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">{{ $schedule->employee?->full_name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $schedule->employee?->employee_code ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->department?->name ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">{{ $schedule->shiftType?->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $schedule->shiftType?->code ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap text-slate-600">
                                    {{ $schedule->start_at?->format('H:i') }} - {{ $schedule->end_at?->format('H:i') }}
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->role_group ?? '-' }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$schedule->status] ?? 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                        {{ $statusLabels[$schedule->status] ?? $schedule->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        @can('duty.update')
                                            <a href="{{ route('duty-schedules.edit', $schedule) }}"
                                               class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                                แก้ไข
                                            </a>
                                        @endcan

                                        @can('duty.delete')
                                            <form method="POST"
                                                  action="{{ route('duty-schedules.destroy', $schedule) }}"
                                                  onsubmit="return confirm('ยืนยันการลบตารางเวรนี้?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded-lg border border-rose-100 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                    ลบ
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <div class="mx-auto max-w-md rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-8">
                                        <p class="font-semibold text-slate-700">ยังไม่มีตารางเวรตามเงื่อนไขนี้</p>
                                        <p class="mt-1 text-sm text-slate-500">ลองเปลี่ยนเดือน หน่วยงาน หรือเริ่มจัดเวรใหม่</p>
                                        @can('duty.create')
                                            <a href="{{ route('duty-schedules.bulk-create') }}"
                                               class="mt-4 inline-flex rounded-xl bg-[#02abff] px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600">
                                                จัดเวรหลายรายการ
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="text-sm text-slate-500">
                        @if ($schedules->total() > 0)
                            แสดง {{ number_format($schedules->firstItem()) }} ถึง {{ number_format($schedules->lastItem()) }}
                            จาก {{ number_format($schedules->total()) }} รายการ
                        @else
                            ไม่มีข้อมูล
                        @endif
                    </div>

                    @if ($schedules->lastPage() > 1)
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($schedules->onFirstPage())
                                <span class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-1.5 text-sm text-slate-400">ก่อนหน้า</span>
                            @else
                                <a href="{{ $schedules->previousPageUrl() }}"
                                   class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">ก่อนหน้า</a>
                            @endif

                            <span class="rounded-lg bg-slate-900 px-3 py-1.5 text-sm font-semibold text-white">
                                {{ $schedules->currentPage() }} / {{ $schedules->lastPage() }}
                            </span>

                            @if ($schedules->hasMorePages())
                                <a href="{{ $schedules->nextPageUrl() }}"
                                   class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">ถัดไป</a>
                            @else
                                <span class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-1.5 text-sm text-slate-400">ถัดไป</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
