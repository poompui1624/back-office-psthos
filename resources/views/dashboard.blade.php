<x-layouts.app title="Dashboard">
    @php
        $can = fn (string $permission): bool => $user?->can($permission) ?? false;
        $visibleOverviewCards = collect($overviewCards)->filter(fn ($card) => $card['href'] && $can($card['permission']));
        $visiblePriorityItems = collect($priorityItems)->filter(fn ($item) => $item['href'] && $can($item['permission']));
        $visibleQuickActions = collect($quickActions)->filter(fn ($action) => $action['href'] && $can($action['permission']));
        $visibleModules = collect($moduleLinks)->filter(fn ($module) => $module['href'] && $can($module['permission']));
        $visibleSupportingStats = collect($supportingStats)->filter(fn ($stat) => $stat['href'] && $can($stat['permission']));
    @endphp

    <div class="min-h-full bg-slate-50/70">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-4 py-6 sm:px-6 lg:px-8">
            @if (session('error') || $errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800 shadow-sm">
                    <div class="font-semibold">ไม่สามารถโหลดข้อมูลบางส่วนได้</div>
                    <div class="mt-1">
                        {{ session('error') ?? $errors->first() }}
                    </div>
                </div>
            @endif

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_18px_60px_rgba(15,23,42,0.08)]">
                <div class="grid gap-0 lg:grid-cols-[1fr_360px]">
                    <div class="p-6 sm:p-8">
                        <div class="inline-flex items-center gap-2 rounded-full border border-[#02abff]/20 bg-[#02abff]/10 px-3 py-1 text-xs font-semibold text-[#027fbd]">
                            <span class="h-2 w-2 rounded-full bg-[#02abff]"></span>
                            Backoffice dashboard
                        </div>

                        <div class="mt-6 max-w-3xl">
                            <p class="text-sm font-medium text-slate-500">ยินดีต้อนรับ {{ $user?->name }}</p>
                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">
                                {{ $hospitalName }}
                            </h1>
                            <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
                                ศูนย์รวมงานสำคัญ รายการรอติดตาม และทางลัดสำหรับการทำงานประจำวัน
                            </p>
                        </div>

                        <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                            @if ($canExportDashboard)
                                <a href="{{ route('exports.dashboard-summary') }}"
                                   data-loading-link
                                   class="inline-flex items-center justify-center rounded-xl bg-[#02abff] px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_32px_rgba(2,171,255,0.28)] transition hover:-translate-y-0.5 hover:bg-[#0299e4] focus:outline-none focus:ring-4 focus:ring-[#02abff]/20">
                                    Export Excel
                                </a>
                            @endif

                            @if ($notificationsUrl)
                                <a href="{{ $notificationsUrl }}"
                                   data-loading-link
                                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-[#02abff]/50 hover:text-[#027fbd] focus:outline-none focus:ring-4 focus:ring-[#02abff]/10">
                                    การแจ้งเตือน
                                </a>
                            @endif
                        </div>
                    </div>

                    <aside class="border-t border-slate-100 bg-slate-50/80 p-6 sm:p-8 lg:border-l lg:border-t-0">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="text-sm font-semibold text-slate-500">สถานะวันนี้</div>
                            <div class="mt-4 space-y-4">
                                @forelse ($visiblePriorityItems->take(3) as $item)
                                    <a href="{{ $item['href'] }}"
                                       data-loading-link
                                       class="group flex items-center justify-between gap-4 rounded-xl border border-slate-100 bg-white px-4 py-3 transition hover:border-[#02abff]/40 hover:bg-[#02abff]/5">
                                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-950">{{ $item['label'] }}</span>
                                        <span class="rounded-full bg-slate-950 px-3 py-1 text-sm font-bold text-white">{{ number_format($item['value']) }}</span>
                                    </a>
                                @empty
                                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                                        ยังไม่มีรายการสำคัญที่ต้องติดตาม
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse ($visibleOverviewCards as $card)
                    <a href="{{ $card['href'] }}"
                       data-loading-link
                       class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_10px_34px_rgba(15,23,42,0.06)] transition hover:-translate-y-1 hover:border-[#02abff]/50 hover:shadow-[0_18px_50px_rgba(2,171,255,0.16)]">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</div>
                                <div class="mt-3 text-3xl font-bold text-slate-950">{{ number_format($card['value']) }}</div>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#02abff]/10 text-sm font-bold text-[#027fbd] transition group-hover:bg-[#02abff] group-hover:text-white">
                                {{ mb_substr($card['label'], 0, 1) }}
                            </div>
                        </div>
                        <div class="mt-4 text-sm leading-6 text-slate-500">{{ $card['helper'] }}</div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center text-sm text-slate-500 sm:col-span-2 xl:col-span-4">
                        ยังไม่มีสถิติที่เปิดให้ดูตามสิทธิ์ผู้ใช้นี้
                    </div>
                @endforelse
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.35fr_0.75fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_10px_34px_rgba(15,23,42,0.06)] sm:p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-950">งานที่ควรติดตาม</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">รายการที่อาจต้องตัดสินใจหรือติดตามต่อในวันนี้</p>
                        </div>

                        @if ($visibleSupportingStats->has('lateThisMonth'))
                            @php($lateStat = $visibleSupportingStats->get('lateThisMonth'))
                            <a href="{{ $lateStat['href'] }}"
                               data-loading-link
                               class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left transition hover:border-[#02abff]/50 hover:bg-[#02abff]/5 sm:text-right">
                                <div class="text-xs font-semibold text-slate-500">{{ $lateStat['label'] }}</div>
                                <div class="mt-1 text-2xl font-bold text-slate-950">{{ number_format($lateStat['value']) }}</div>
                            </a>
                        @endif
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-2">
                        @forelse ($visiblePriorityItems as $item)
                            <a href="{{ $item['href'] }}"
                               data-loading-link
                               class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-[#02abff]/50 hover:bg-[#02abff]/5">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800">{{ $item['label'] }}</div>
                                        <div class="mt-1 text-xs text-slate-500">เปิดดูรายละเอียด</div>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-950 group-hover:text-[#027fbd]">{{ number_format($item['value']) }}</div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500 md:col-span-2">
                                ยังไม่มีงานเร่งด่วนสำหรับสิทธิ์ผู้ใช้นี้
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_10px_34px_rgba(15,23,42,0.06)] sm:p-6">
                    <h2 class="text-xl font-bold text-slate-950">ทางลัด</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500">เริ่มงานที่ใช้บ่อยได้เร็วขึ้น</p>

                    <div class="mt-6 grid gap-3">
                        @forelse ($visibleQuickActions as $action)
                            <a href="{{ $action['href'] }}"
                               data-loading-link
                               class="group flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-[#02abff]/50 hover:bg-[#02abff]/5">
                                <span>{{ $action['label'] }}</span>
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-400 transition group-hover:bg-[#02abff] group-hover:text-white">›</span>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-8 text-center text-sm text-slate-500">
                                ยังไม่มีทางลัดสำหรับสิทธิ์ผู้ใช้นี้
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_10px_34px_rgba(15,23,42,0.06)] sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-950">ระบบงานหลัก</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-500">เลือกโมดูลที่ต้องการทำงานต่อ ระบบจะแสดงเฉพาะเมนูที่คุณมีสิทธิ์</p>
                    </div>
                    <div class="rounded-full bg-[#02abff]/10 px-4 py-2 text-xs font-semibold text-[#027fbd]">
                        {{ $visibleModules->count() }} โมดูลพร้อมใช้งาน
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($visibleModules as $module)
                        <a href="{{ $module['href'] }}"
                           data-loading-link
                           class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-[#02abff]/50 hover:shadow-[0_18px_48px_rgba(2,171,255,0.14)]">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-base font-bold text-slate-950">{{ $module['label'] }}</div>
                                    <div class="mt-2 text-sm leading-6 text-slate-500">{{ $module['description'] }}</div>
                                </div>
                                <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-400 transition group-hover:bg-[#02abff] group-hover:text-white">›</span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-10 text-center text-sm text-slate-500 md:col-span-2 xl:col-span-3">
                            ยังไม่มีโมดูลที่เปิดให้ใช้งานสำหรับสิทธิ์ผู้ใช้นี้
                        </div>
                    @endforelse
                </div>
            </section>

            @if ($visibleSupportingStats->except('lateThisMonth')->isNotEmpty())
                <section class="grid gap-4 md:grid-cols-2">
                    @foreach ($visibleSupportingStats->except('lateThisMonth') as $stat)
                        <a href="{{ $stat['href'] }}"
                           data-loading-link
                           class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#02abff]/50 hover:bg-[#02abff]/5">
                            <div class="text-sm font-semibold text-slate-500">{{ $stat['label'] }}</div>
                            <div class="mt-3 text-3xl font-bold text-slate-950">{{ number_format($stat['value']) }}</div>
                        </a>
                    @endforeach
                </section>
            @endif
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-loading-link]').forEach((link) => {
            link.addEventListener('click', () => {
                link.setAttribute('aria-busy', 'true');
                link.classList.add('pointer-events-none', 'opacity-70');
            });
        });
    </script>
</x-layouts.app>
