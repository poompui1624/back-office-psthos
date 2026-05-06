<x-layouts.app title="เพิ่มปีงบประมาณ ITA">
    <div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">เพิ่มปีงบประมาณ ITA</h1>
            <p class="mt-1 text-sm text-gray-500">
                ใช้สำหรับแยกชุดหัวข้อ MOIT และเอกสารตามปีงบประมาณ
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('ita.fiscal-years.store') }}" class="space-y-5">
                @csrf

                @include('ita.fiscal-years.form', [
                    'fiscalYear' => null,
                ])

                <div class="flex justify-end gap-2">
                    <a href="{{ route('ita.fiscal-years.index') }}"
                       class="rounded bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">
                        ยกเลิก
                    </a>

                    <button class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
