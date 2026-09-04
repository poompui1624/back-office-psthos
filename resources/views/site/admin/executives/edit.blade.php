<x-layouts.app title="แก้ไขผู้บริหาร">
    @include('site.admin._nav')

    <x-page-header title="แก้ไขผู้บริหาร" subtitle="รายนามผู้บริหารที่แสดงบนหน้าเว็บ" />

    <div class="card card-pad">
        <form method="POST" action="{{ route('site.executives.update', $executive) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            @include('site.admin.executives._form')

            <x-form.actions :cancel="route('site.executives.index')" />
        </form>
    </div>
</x-layouts.app>
