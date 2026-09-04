<x-layouts.app title="เพิ่มประเภทการลา">
    <x-page-header title="เพิ่มประเภทการลา" subtitle="เพิ่มประเภทการลาใหม่เข้าสู่ระบบ" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('leave-types.store') }}" class="space-y-6">
            @csrf

            @include('leave-types._form')

            <x-form.actions :cancel="route('leave-types.index')" />
        </form>
    </div>
</x-layouts.app>
