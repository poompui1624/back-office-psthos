<x-layouts.app title="แก้ไขประเภทการลา">
    <x-page-header title="แก้ไขประเภทการลา" :subtitle="$leaveType->code . ' - ' . $leaveType->name" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('leave-types.update', $leaveType) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('leave-types._form')

            <x-form.actions :cancel="route('leave-types.index')" />
        </form>
    </div>
</x-layouts.app>
