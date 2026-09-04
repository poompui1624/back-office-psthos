<x-layouts.app title="แก้ไขเนื้อหา">
    @include('site.admin._nav')

    <x-page-header title="แก้ไขเนื้อหา" :subtitle="$post->title">
        @if ($post->is_published && $post->published_at && $post->published_at->isPast())
            <x-btn :href="route('site.post', $post->slug)" target="_blank" variant="secondary" icon="external-link">
                ดูหน้านี้
            </x-btn>
        @endif
    </x-page-header>

    <div class="card card-pad">
        <form method="POST" action="{{ route('site.posts.update', $post) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            @include('site.admin.posts._form')

            <x-form.actions :cancel="route('site.posts.index')" />
        </form>
    </div>

    @if ($post->images->isNotEmpty())
        <div class="card card-pad mt-6">
            <h2 class="section-title mb-4">ภาพประกอบ ({{ $post->images->count() }})</h2>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($post->images as $image)
                    <figure class="overflow-hidden rounded-xl ring-1 ring-slate-200">
                        <img src="{{ $image->image_url }}" alt="" class="aspect-square w-full object-cover">

                        <figcaption class="flex items-center justify-between gap-2 p-2">
                            <span class="min-w-0 truncate text-xs text-slate-500">{{ $image->caption ?: 'ไม่มีคำบรรยาย' }}</span>

                            @can('site.manage')
                                <form method="POST"
                                      action="{{ route('site.posts.images.destroy', [$post, $image]) }}"
                                      onsubmit="return confirm('ยืนยันการลบภาพนี้?')">
                                    @csrf
                                    @method('DELETE')

                                    <x-btn type="submit" variant="danger" size="sm">ลบ</x-btn>
                                </form>
                            @endcan
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>
