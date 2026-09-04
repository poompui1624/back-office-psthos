<x-layouts.app title="แก้ไขประเภทเวร">
    <x-page-header title="แก้ไขประเภทเวร" :subtitle="$shiftType->code . ' - ' . $shiftType->name" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('shift-types.update', $shiftType) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('shift-types._form')

            <x-form.actions :cancel="route('shift-types.index')" />
        </form>
    </div>
</x-layouts.app>
