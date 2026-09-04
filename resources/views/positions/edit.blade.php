<x-layouts.app title="แก้ไขตำแหน่ง">
    <x-page-header title="แก้ไขตำแหน่ง" :subtitle="$position->name" />

    <div class="card card-pad max-w-3xl">
        <form method="POST" action="{{ route('positions.update', $position) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('positions._form', ['position' => $position])

            <x-form.actions :cancel="route('positions.index')" />
        </form>
    </div>
</x-layouts.app>
