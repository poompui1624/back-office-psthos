<x-layouts.app>
    @include('me._header', ['employee' => $employee, 'title' => 'ภาพรวมของฉัน', 'subtitle' => 'สรุปข้อมูลส่วนตัวในระบบ'])

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">ใบลารออนุมัติ</div>
            <div class="mt-1 text-3xl font-bold text-slate-900">{{ $pendingLeaveCount }}</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">วันลาที่อนุมัติปีนี้</div>
            <div class="mt-1 text-3xl font-bold text-slate-900">{{ rtrim(rtrim(number_format($approvedLeaveDays, 1), '0'), '.') }}</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">แจ้งซ่อมที่ยังไม่ปิด</div>
            <div class="mt-1 text-3xl font-bold text-slate-900">{{ $openRepairs }}</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-slate-500">สลิปล่าสุด</div>
            <div class="mt-1 text-lg font-bold text-slate-900">
                @if ($latestPayslip?->payrollPeriod)
                    {{ $latestPayslip->payrollPeriod->name }}
                @else
                    <span class="text-slate-400">ยังไม่มี</span>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-bold text-slate-900">เวรที่กำลังจะถึง</h2>

                @can('duty.view.own')
                    <a href="{{ route('me.duties') }}" class="text-sm font-semibold text-[#02abff] hover:underline">ดูทั้งหมด</a>
                @endcan
            </div>

            @forelse ($upcomingDuties as $duty)
                <div class="flex items-center justify-between border-b border-slate-100 py-2 last:border-0">
                    <div class="text-sm text-slate-700">{{ $duty->work_date?->format('d/m/Y') }}</div>
                    <div class="text-sm font-medium text-slate-900">{{ $duty->shiftType?->name ?? '-' }}</div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-slate-500">ยังไม่มีเวรที่จัดไว้ล่วงหน้า</p>
            @endforelse
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-bold text-slate-900">ใบลาล่าสุด</h2>

                @can('leave.create')
                    <a href="{{ route('leave-requests.create') }}" class="rounded-lg bg-[#02abff] px-3 py-1.5 text-sm font-semibold text-white hover:bg-sky-500">
                        ยื่นใบลา
                    </a>
                @endcan
            </div>

            @forelse ($recentLeaves as $leave)
                <div class="flex items-center justify-between border-b border-slate-100 py-2 last:border-0">
                    <div class="min-w-0">
                        <div class="truncate text-sm font-medium text-slate-900">{{ $leave->leaveType?->name ?? '-' }}</div>
                        <div class="text-xs text-slate-500">
                            {{ $leave->start_date?->format('d/m/Y') }} &ndash; {{ $leave->end_date?->format('d/m/Y') }}
                        </div>
                    </div>

                    @include('me._status', ['status' => $leave->status])
                </div>
            @empty
                <p class="py-6 text-center text-sm text-slate-500">ยังไม่เคยยื่นใบลา</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>
