<x-layouts.app title="เพิ่มบุคลากร">
    <x-page-header title="เพิ่มบุคลากร" subtitle="เพิ่มข้อมูลบุคลากรเข้าสู่ระบบกลาง" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('employees.store') }}" class="space-y-6">
            @csrf

            @include('employees._form')

            <x-form.actions :cancel="route('employees.index')" />
        </form>
    </div>
</x-layouts.app>
