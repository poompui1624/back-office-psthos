<x-layouts.app title="เพิ่มปีงบประมาณ ITA">
    <div class="mx-auto w-full max-w-3xl">
        <x-page-header title="เพิ่มปีงบประมาณ ITA"
                       subtitle="ใช้สำหรับแยกชุดหัวข้อ MOIT และเอกสารตามปีงบประมาณ" />

        <div class="card card-pad">
            <form method="POST" action="{{ route('ita.fiscal-years.store') }}" class="space-y-5">
                @csrf

                @include('ita.fiscal-years.form', ['fiscalYear' => null])

                <x-form.actions :cancel="route('ita.fiscal-years.index')" cancel-label="ยกเลิก" />
            </form>
        </div>
    </div>
</x-layouts.app>
