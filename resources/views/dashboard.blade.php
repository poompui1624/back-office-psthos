<x-layouts.app title="Executive Dashboard" subtitle="ภาพรวม KPI งานบุคคล เวร พัสดุ และการแจ้งเตือน">
    <div class="space-y-6">
        {{-- Hero --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 via-brand-500 to-brand-700 px-6 py-7 text-white shadow-xl shadow-brand-500/20 sm:px-8 sm:py-8">
            <div class="pointer-events-none absolute -right-16 -top-24 h-72 w-72 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-28 right-24 h-64 w-64 rounded-full bg-white/5"></div>

            <div class="relative">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-2xl font-bold sm:text-3xl">
                        {{ $greeting }}, {{ $user?->name }} <span class="inline-block">👋</span>
                    </h2>

                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold ring-1 ring-white/25">
                        ขอบเขต: {{ $scopeLabel }}
                    </span>
                </div>

                <p class="mt-2 max-w-3xl text-sm leading-relaxed text-white/85">
                    ภาพรวม{{ $hospitalName }} วันที่ {{ now()->format('d/m/') . thai_year((int) now()->year) }}
                    — ตัวเลขทั้งหมดในหน้านี้จำกัดตามสิทธิ์การเข้าถึงของคุณ
                </p>

                <div class="mt-6 flex flex-wrap gap-x-10 gap-y-4">
                    @foreach ($heroStats as $stat)
                        <div>
                            <div class="text-3xl font-bold leading-none sm:text-4xl">{{ $stat['value'] }}</div>
                            <div class="mt-1.5 text-xs text-white/75">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>

                @php
                    $actions = collect($quickActions)->filter(fn ($a) => $a['href'] && auth()->user()?->can($a['permission']));
                @endphp

                @if ($actions->isNotEmpty())
                    <div class="mt-7 flex flex-wrap gap-2">
                        @foreach ($actions as $action)
                            <a href="{{ $action['href'] }}"
                               class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold ring-1 ring-white/25 transition hover:bg-white/25">
                                <x-icon :name="$action['icon']" class="h-4 w-4" />
                                {{ $action['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- KPI cards --}}
        @php
            $visibleCards = collect($statCards)->filter(fn ($c) => auth()->user()?->can($c['permission']));
        @endphp

        @if ($visibleCards->isNotEmpty())
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($visibleCards as $card)
                    <x-stat-card
                        :label="$card['label']"
                        :value="$card['value']"
                        :icon="$card['icon']"
                        :tone="$card['tone']"
                        :delta="$card['delta'] ?? null"
                        :helper="$card['helper'] ?? null"
                        :href="$card['href']"
                        :featured="$card['featured'] ?? false" />
                @endforeach
            </section>
        @endif

        {{-- Alerts --}}
        @php
            $visibleAlerts = collect($alertCards)->filter(fn ($a) => auth()->user()?->can($a['permission']));
        @endphp

        @if ($visibleAlerts->isNotEmpty())
            <section>
                <h3 class="mb-3 flex items-center gap-2 text-sm font-bold text-slate-900">
                    <x-icon name="bell" class="h-4 w-4 text-amber-500" />
                    ต้องติดตาม
                </h3>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($visibleAlerts as $alert)
                        <a @if ($alert['href']) href="{{ $alert['href'] }}" @endif
                           class="card group flex items-center gap-4 border-l-4 p-4 transition hover:-translate-y-0.5"
                           style="border-left-color: {{ $alert['accent'] }}">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                                 style="background: {{ $alert['accent'] }}1a; color: {{ $alert['accent'] }}">
                                <x-icon :name="$alert['icon']" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="truncate text-xs font-semibold text-slate-500">{{ $alert['label'] }}</div>

                                <div class="mt-0.5 flex items-baseline gap-1.5">
                                    <span class="text-2xl font-bold text-slate-900">{{ number_format($alert['count']) }}</span>
                                    <span class="text-xs text-slate-500">{{ $alert['unit'] }}</span>
                                </div>

                                <div class="mt-0.5 truncate text-[11px] text-slate-400">{{ $alert['note'] }}</div>
                            </div>

                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-slate-500" />
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Donuts --}}
        <section class="grid gap-6 xl:grid-cols-2">
            @can('employee.view')
                <div class="card card-pad">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <h3 class="section-title">บุคลากรตามหน่วยงาน</h3>
                        <span class="muted text-xs">เฉพาะสถานะปฏิบัติงาน</span>
                    </div>

                    <x-chart.donut :segments="$employeesByDepartment" caption="คน" />
                </div>
            @endcan

            @can('leave.view')
                <div class="card card-pad">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <h3 class="section-title">สถานะใบลาปีนี้</h3>
                        <span class="muted text-xs">พ.ศ. {{ thai_year((int) now()->year) }}</span>
                    </div>

                    <x-chart.donut :segments="$leaveByStatus" caption="ใบลา" />
                </div>
            @endcan
        </section>


        {{-- Trend + department value --}}
        <section class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
            @canany(['leave.view', 'duty.view'])
                <div class="card card-pad">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="section-title">แนวโน้ม 12 เดือนย้อนหลัง</h3>
                        <span class="muted text-xs">วันลา vs จำนวนเวร</span>
                    </div>

                    <x-chart.line :series="$leaveTrend['series']" :labels="$leaveTrend['labels']" />
                </div>
            @endcanany

            @can('asset.view')
                <div class="card card-pad">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <h3 class="section-title">มูลค่าพัสดุตามหน่วยงาน</h3>
                        <span class="muted text-xs">Top 6</span>
                    </div>

                    <x-chart.bar-list :rows="$assetsByDepartment" format="money" empty="ยังไม่มีพัสดุที่บันทึกราคา" />
                </div>
            @endcan
        </section>

        {{-- Expirations + activity --}}
        <section class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
            @can('software.view')
                <div class="card overflow-hidden">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                        <h3 class="section-title">License ที่ใกล้หมดอายุ</h3>

                        <a href="{{ route('software-licenses.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">
                            ดูทั้งหมด
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50/80 text-left text-xs text-slate-500">
                                <tr>
                                    <th class="px-5 py-3 font-semibold sm:px-6">รายการ</th>
                                    <th class="px-5 py-3 font-semibold">วันหมดอายุ</th>
                                    <th class="px-5 py-3 text-right font-semibold sm:px-6">สถานะ</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @forelse ($upcomingExpirations as $row)
                                    <tr class="transition hover:bg-slate-50/70">
                                        <td class="px-5 py-3 sm:px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                                    <x-icon name="key" class="h-[18px] w-[18px]" />
                                                </div>
                                                <span class="font-medium text-slate-900">{{ $row['name'] }}</span>
                                            </div>
                                        </td>

                                        <td class="px-5 py-3 text-slate-600">
                                            {{ $row['expiry']->format('d/m/') . thai_year((int) $row['expiry']->year) }}
                                        </td>

                                        <td class="px-5 py-3 text-right sm:px-6">
                                            <span @class([
                                                'pill',
                                                'bg-rose-100 text-rose-700' => $row['tone'] === 'rose',
                                                'bg-amber-100 text-amber-700' => $row['tone'] === 'amber',
                                                'bg-slate-100 text-slate-600' => $row['tone'] === 'slate',
                                            ])>{{ $row['label'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center text-sm text-slate-500">
                                            ไม่มี License ที่ใกล้หมดอายุใน 90 วัน
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endcan

            @can('audit.view')
                <div class="card card-pad">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <h3 class="section-title">ความเคลื่อนไหวล่าสุด</h3>

                        <a href="{{ route('audit-logs.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">
                            ดูทั้งหมด
                        </a>
                    </div>

                    @forelse ($recentActivities as $activity)
                        <div class="relative flex gap-3 pb-5 last:pb-0">
                            @unless ($loop->last)
                                <span class="absolute left-[5px] top-4 h-full w-px bg-slate-200"></span>
                            @endunless

                            <span class="relative mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full ring-4 ring-white"
                                  style="background: {{ $activity['tone'] }}"></span>

                            <div class="min-w-0 flex-1">
                                <div class="text-sm text-slate-900">
                                    <span class="font-semibold">{{ $activity['action'] }}</span>
                                    {{ $activity['module'] }}
                                </div>

                                <div class="mt-0.5 flex flex-wrap items-center gap-x-2 text-[11px] text-slate-500">
                                    <span>{{ $activity['user'] }}</span>
                                    <span>&middot;</span>
                                    <span>{{ $activity['at']?->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-12 text-center text-sm text-slate-500">ยังไม่มีความเคลื่อนไหว</p>
                    @endforelse
                </div>
            @endcan
        </section>

    </div>
</x-layouts.app>
