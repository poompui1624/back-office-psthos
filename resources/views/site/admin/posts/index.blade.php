<x-layouts.app title="ข่าวและกิจกรรม">
    @include('site.admin._nav')

    <x-page-header title="ข่าวและกิจกรรม" subtitle="ข่าวประชาสัมพันธ์ ภาพกิจกรรม และความรู้สู่ประชาชน">
        @can('site.manage')
            <x-btn :href="route('site.posts.create')" icon="document">เขียนเนื้อหาใหม่</x-btn>
        @endcan
    </x-page-header>

    <x-filter-bar :action="route('site.posts.index')">
        <x-form.field label="ค้นหา" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="ชื่อเรื่อง" />
        </x-form.field>

        <x-form.field label="หมวด">
            <x-form.select name="category" class="w-48">
                <option value="">ทุกหมวด</option>

                @foreach (\App\Models\SitePost::CATEGORIES as $value => $label)
                    <option value="{{ $value }}" @selected($category === $value)>{{ $label }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>
    </x-filter-bar>

    <x-data-table>
        <x-slot:head>
            <x-data-table.th>เรื่อง</x-data-table.th>
            <x-data-table.th>หมวด</x-data-table.th>
            <x-data-table.th>เผยแพร่</x-data-table.th>
            <x-data-table.th align="center">ภาพ</x-data-table.th>
            <x-data-table.th align="center">เปิดอ่าน</x-data-table.th>
            <x-data-table.th align="center">สถานะ</x-data-table.th>
            <x-data-table.th align="center">จัดการ</x-data-table.th>
        </x-slot:head>

        @forelse ($posts as $post)
            @php
                $isLive = $post->is_published && $post->published_at && $post->published_at->isPast();
            @endphp

            <x-data-table.row>
                <x-data-table.td>
                    <div class="flex items-center gap-3">
                        @if ($post->cover_image_url)
                            <img src="{{ $post->cover_image_url }}" alt=""
                                 class="h-11 w-16 shrink-0 rounded-lg object-cover ring-1 ring-slate-200">
                        @else
                            <span class="flex h-11 w-16 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                <x-icon name="document" class="h-4 w-4" />
                            </span>
                        @endif

                        <div class="min-w-0">
                            <div class="font-medium text-slate-900">{{ $post->title }}</div>

                            @if ($post->is_pinned)
                                <div class="mt-1"><x-badge tone="warning">ปักหมุด</x-badge></div>
                            @endif
                        </div>
                    </div>
                </x-data-table.td>

                <x-data-table.td>{{ $post->category_label }}</x-data-table.td>

                <x-data-table.td class="whitespace-nowrap text-xs">
                    {{ $post->published_at?->format('Y-m-d H:i') ?? '—' }}
                </x-data-table.td>

                <x-data-table.td align="center">{{ $post->images_count }}</x-data-table.td>

                <x-data-table.td align="center" class="tabular-nums">{{ number_format($post->view_count) }}</x-data-table.td>

                <x-data-table.td align="center">
                    @if (! $post->is_published)
                        <x-badge tone="slate" dot>ฉบับร่าง</x-badge>
                    @elseif ($isLive)
                        <x-badge tone="success" dot>เผยแพร่แล้ว</x-badge>
                    @else
                        <x-badge tone="info" dot>ตั้งเวลาไว้</x-badge>
                    @endif
                </x-data-table.td>

                <x-data-table.td align="center">
                    <div class="flex flex-wrap justify-center gap-2">
                        @if ($isLive)
                            <x-btn :href="route('site.post', $post->slug)" target="_blank" variant="secondary" size="sm">ดู</x-btn>
                        @endif

                        @can('site.manage')
                            <x-btn :href="route('site.posts.edit', $post)" variant="secondary" size="sm">แก้ไข</x-btn>

                            <form method="POST" action="{{ route('site.posts.destroy', $post) }}"
                                  onsubmit="return confirm('ยืนยันการลบเนื้อหานี้?')">
                                @csrf
                                @method('DELETE')

                                <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                            </form>
                        @endcan
                    </div>
                </x-data-table.td>
            </x-data-table.row>
        @empty
            <x-data-table.empty :colspan="7" icon="document" title="ยังไม่มีเนื้อหา"
                                description="เขียนข่าวหรืออัปโหลดภาพกิจกรรมเพื่อแสดงบนหน้าเว็บ">
                @can('site.manage')
                    <x-btn :href="route('site.posts.create')">เขียนเนื้อหาใหม่</x-btn>
                @endcan
            </x-data-table.empty>
        @endforelse
    </x-data-table>

    <div class="mt-4">{{ $posts->links() }}</div>
</x-layouts.app>
