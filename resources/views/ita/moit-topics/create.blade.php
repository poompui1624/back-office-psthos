<x-layouts.app title="เพิ่มหัวข้อหลัก MOIT">
    <div class="mx-auto w-full max-w-4xl">
        <x-page-header title="เพิ่มหัวข้อหลัก MOIT" />

        <div class="card card-pad">
            <form method="POST" action="{{ route('ita.moit-topics.store') }}" class="space-y-5">
                @csrf

                @include('ita.moit-topics.form', [
                    'topic' => null,
                    'fiscalYears' => $fiscalYears,
                ])

                <x-form.actions :cancel="route('ita.moit-topics.index')" cancel-label="ยกเลิก" />
            </form>
        </div>
    </div>
</x-layouts.app>
