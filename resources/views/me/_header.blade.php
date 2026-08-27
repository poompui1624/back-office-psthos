@props(['employee', 'title', 'subtitle' => null])

<div class="mb-6">
    <div class="rounded-2xl bg-[#02abff] px-5 py-5 text-white sm:px-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="text-xs font-semibold uppercase tracking-wide text-sky-100">ของฉัน</div>
                <h1 class="mt-1 truncate text-xl font-bold sm:text-2xl">{{ $title }}</h1>

                @if ($subtitle)
                    <p class="mt-1 text-sm text-sky-50">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="text-right text-sm">
                <div class="font-semibold">{{ $employee->full_name }}</div>
                <div class="text-sky-100">
                    {{ $employee->employee_code }}
                    @if ($employee->department)
                        &middot; {{ $employee->department->name }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @php
            $tabs = [
                ['label' => 'ภาพรวม', 'route' => 'me.index', 'permission' => null],
                ['label' => 'ใบลาของฉัน', 'route' => 'me.leaves', 'permission' => 'leave.view.own'],
                ['label' => 'เวรของฉัน', 'route' => 'me.duties', 'permission' => 'duty.view.own'],
                ['label' => 'ลงเวลาของฉัน', 'route' => 'me.attendance', 'permission' => 'attendance.view.own'],
                ['label' => 'สลิปของฉัน', 'route' => 'me.payslips', 'permission' => 'payslip.view.own'],
                ['label' => 'แจ้งซ่อมของฉัน', 'route' => 'me.repairs', 'permission' => 'repair.view.own'],
                ['label' => 'จองห้องของฉัน', 'route' => 'me.meetings', 'permission' => 'meeting.view.own'],
            ];
        @endphp

        @foreach ($tabs as $tab)
            @if ($tab['permission'] === null || auth()->user()?->can($tab['permission']))
                <a href="{{ route($tab['route']) }}"
                   class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs($tab['route']) ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 shadow-sm hover:bg-slate-50' }}">
                    {{ $tab['label'] }}
                </a>
            @endif
        @endforeach
    </div>
</div>
