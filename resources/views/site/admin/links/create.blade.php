<x-layouts.app title="เพิ่มลิงก์สำคัญ">
    @include('site.admin._nav')

    <x-page-header title="เพิ่มลิงก์สำคัญ" subtitle="ปุ่มลัดที่แสดงเป็นตารางไอคอนบนหน้าเว็บ" />

    <div class="card card-pad">
        <form method="POST" action="{{ route('site.links.store') }}" class="space-y-5">
            @csrf

            @include('site.admin.links._form')

            <x-form.actions :cancel="route('site.links.index')" />
        </form>
    </div>
</x-layouts.app>
