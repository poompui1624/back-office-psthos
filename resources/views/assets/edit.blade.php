<x-layouts.app title="แก้ไขพัสดุ">
    <x-page-header title="แก้ไขพัสดุ" :subtitle="$asset->asset_code . ' - ' . $asset->name" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('assets.update', $asset) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('assets._form')

            <x-form.actions :cancel="route('assets.index')" />
        </form>
    </div>
</x-layouts.app>
