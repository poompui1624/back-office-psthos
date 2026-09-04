<x-layouts.app title="แก้ไขผู้ใช้งาน">
    <x-page-header title="แก้ไขผู้ใช้งาน" subtitle="{{ $user->name }} / {{ $user->email }}" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('users._form')

            <x-form.actions :cancel="route('users.index')" submit-label="บันทึกการแก้ไข" />
        </form>
    </div>
</x-layouts.app>
