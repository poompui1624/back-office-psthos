<x-layouts.app title="แก้ไข Software">
    <x-page-header title="แก้ไข Software" :subtitle="$softwareProduct->name" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('software-products.update', $softwareProduct) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('software-products._form')

            <x-form.actions :cancel="route('software-products.index')" />
        </form>
    </div>
</x-layouts.app>
