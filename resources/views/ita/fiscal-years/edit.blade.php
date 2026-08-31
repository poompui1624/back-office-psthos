<x-layouts.app title="แก้ไขปีงบประมาณ ITA">
    <div class="mx-auto w-full max-w-3xl">
        <x-page-header title="แก้ไขปีงบประมาณ ITA"
                       subtitle="แก้ไขชื่อหรือสถานะเปิดใช้งานของปีงบประมาณ" />

        <div class="card card-pad">
            <form method="POST" action="{{ route('ita.fiscal-years.update', $fiscalYear) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('ita.fiscal-years.form', ['fiscalYear' => $fiscalYear])

                <x-form.actions :cancel="route('ita.fiscal-years.index')" cancel-label="ยกเลิก" />
            </form>
        </div>
    </div>
</x-layouts.app>
