<x-layouts.app title="แก้ไขคอมพิวเตอร์">
    <x-page-header title="แก้ไขคอมพิวเตอร์" :subtitle="$computer->hostname" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('computers.update', $computer) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('computers._form')

            <x-form.actions :cancel="route('computers.index')" />
        </form>
    </div>
</x-layouts.app>
