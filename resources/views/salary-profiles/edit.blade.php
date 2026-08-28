<x-layouts.app title="แก้ไขข้อมูลเงินเดือน">
    <x-page-header title="แก้ไขข้อมูลเงินเดือน"
                   :subtitle="$salaryProfile->employee?->full_name ?? 'ตั้งค่าเงินเดือนและรายการหักของบุคลากร'" />

    <div class="card card-pad max-w-4xl">
        <form method="POST" action="{{ route('salary-profiles.update', $salaryProfile) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('salary-profiles._form')

            <x-form.actions :cancel="route('salary-profiles.index')" />
        </form>
    </div>
</x-layouts.app>
