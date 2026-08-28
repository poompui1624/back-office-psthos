<x-layouts.app title="เพิ่มตำแหน่ง">
    <x-page-header title="เพิ่มตำแหน่ง" subtitle="กำหนดตำแหน่งงานสำหรับบุคลากร" />

    <div class="card card-pad max-w-3xl">
        <form method="POST" action="{{ route('positions.store') }}" class="space-y-6">
            @csrf

            @include('positions._form', ['position' => null])

            <x-form.actions :cancel="route('positions.index')" />
        </form>
    </div>
</x-layouts.app>
