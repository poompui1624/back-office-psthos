<x-layouts.app title="แจ้งซ่อมใหม่">
    <x-page-header title="แจ้งซ่อมใหม่" subtitle="บันทึกรายการแจ้งซ่อมเข้าสู่ระบบ" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('repair-requests.store') }}" class="space-y-6">
            @csrf

            @include('repair-requests._form')

            <x-form.actions :cancel="route('repair-requests.index')" submit-label="บันทึกแจ้งซ่อม" />
        </form>
    </div>
</x-layouts.app>
