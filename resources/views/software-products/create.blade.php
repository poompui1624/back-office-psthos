<x-layouts.app title="เพิ่ม Software">
    <x-page-header title="เพิ่ม Software" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('software-products.store') }}" class="space-y-6">
            @csrf
            @include('software-products._form')

            <x-form.actions :cancel="route('software-products.index')" />
        </form>
    </div>
</x-layouts.app>
