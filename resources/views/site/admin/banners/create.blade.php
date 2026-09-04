<x-layouts.app title="เพิ่มแบนเนอร์">
    @include('site.admin._nav')

    <x-page-header title="เพิ่มแบนเนอร์" subtitle="ภาพสไลด์บนสุดของหน้าเว็บโรงพยาบาล" />

    <div class="card card-pad">
        <form method="POST" action="{{ route('site.banners.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @include('site.admin.banners._form')

            <x-form.actions :cancel="route('site.banners.index')" />
        </form>
    </div>
</x-layouts.app>
