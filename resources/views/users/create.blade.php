<x-layouts.app title="เพิ่มผู้ใช้งาน">
    <x-page-header title="เพิ่มผู้ใช้งาน" subtitle="สร้างบัญชี Login และกำหนด Role" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf

            @include('users._form')

            <x-form.actions :cancel="route('users.index')" />
        </form>
    </div>
</x-layouts.app>
