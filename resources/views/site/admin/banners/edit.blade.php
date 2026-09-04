<x-layouts.app title="แก้ไขแบนเนอร์">
    @include('site.admin._nav')

    <x-page-header title="แก้ไขแบนเนอร์" subtitle="ภาพสไลด์บนสุดของหน้าเว็บโรงพยาบาล" />

    <div class="card card-pad">
        <form method="POST" action="{{ route('site.banners.update', $banner) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            @include('site.admin.banners._form')

            <x-form.actions :cancel="route('site.banners.index')" />
        </form>
    </div>
</x-layouts.app>
