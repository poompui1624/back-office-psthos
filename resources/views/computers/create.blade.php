<x-layouts.app title="เพิ่มคอมพิวเตอร์">
    <x-page-header title="เพิ่มคอมพิวเตอร์" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('computers.store') }}" class="space-y-6">
            @csrf

            @include('computers._form')

            <x-form.actions :cancel="route('computers.index')" />
        </form>
    </div>
</x-layouts.app>
