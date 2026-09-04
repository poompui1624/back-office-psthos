<x-layouts.app title="ข้อมูลโรงพยาบาล">
    @include('site.admin._nav')

    <x-page-header title="ข้อมูลโรงพยาบาล"
                   subtitle="ประวัติ วิสัยทัศน์ และโครงสร้าง ที่แสดงบนหน้าเว็บ" />

    {{-- Three fixed pages the homepage places by key, so they are edited rather
         than created or deleted. --}}
    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($pages as $page)
            <article class="card card-pad flex flex-col">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="font-bold text-slate-900">{{ $page->title }}</h2>

                    <x-badge :tone="$page->is_active ? 'success' : 'slate'" dot>
                        {{ $page->is_active ? 'แสดง' : 'ซ่อน' }}
                    </x-badge>
                </div>

                <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-600">
                    @if ($page->body)
                        {{ Str::limit($page->body, 140) }}
                    @else
                        <span class="text-slate-400">ยังไม่มีเนื้อหา</span>
                    @endif
                </p>

                @can('site.manage')
                    <div class="mt-5">
                        <x-btn :href="route('site.pages.edit', $page)" variant="secondary" size="sm">แก้ไข</x-btn>
                    </div>
                @endcan
            </article>
        @endforeach
    </div>
</x-layouts.app>
