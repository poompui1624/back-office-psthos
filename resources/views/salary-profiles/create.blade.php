<x-layouts.app title="เพิ่มข้อมูลเงินเดือน">
    <x-page-header title="เพิ่มข้อมูลเงินเดือน" subtitle="ตั้งค่าเงินเดือนและรายการหักของบุคลากร" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('salary-profiles.store') }}" class="space-y-6">
            @csrf

            @include('salary-profiles._form')

            <x-form.actions :cancel="route('salary-profiles.index')" />
        </form>
    </div>
</x-layouts.app>
