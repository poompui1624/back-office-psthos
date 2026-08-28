<x-layouts.app title="สร้าง Computer Agent">
    <x-page-header title="สร้าง Computer Agent" subtitle="สร้าง Token สำหรับให้โปรแกรม Agent ส่งข้อมูลเครื่อง Client เข้า Server" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('computer-agents.store') }}" class="space-y-6">
            @csrf

            @include('computer-agents._form')

            <x-form.actions :cancel="route('computer-agents.index')" />
        </form>
    </div>
</x-layouts.app>
