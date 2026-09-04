<x-layouts.app title="แก้ไขลิงก์สำคัญ">
    @include('site.admin._nav')

    <x-page-header title="แก้ไขลิงก์สำคัญ" subtitle="ปุ่มลัดที่แสดงเป็นตารางไอคอนบนหน้าเว็บ" />

    <div class="card card-pad">
        <form method="POST" action="{{ route('site.links.update', $link) }}" class="space-y-5">
            @csrf
            @method('PUT')

            @include('site.admin.links._form')

            <x-form.actions :cancel="route('site.links.index')" />
        </form>
    </div>
</x-layouts.app>
