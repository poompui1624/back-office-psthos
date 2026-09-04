<x-layouts.app title="เขียนเนื้อหาใหม่">
    @include('site.admin._nav')

    <x-page-header title="เขียนเนื้อหาใหม่" subtitle="ข่าวประชาสัมพันธ์ ภาพกิจกรรม หรือความรู้สู่ประชาชน" />

    <div class="card card-pad">
        <form method="POST" action="{{ route('site.posts.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @include('site.admin.posts._form')

            <x-form.actions :cancel="route('site.posts.index')" />
        </form>
    </div>
</x-layouts.app>
