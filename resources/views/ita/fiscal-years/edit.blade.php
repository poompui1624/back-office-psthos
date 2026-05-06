<x-layouts.app title="แก้ไขปีงบประมาณ ITA">
    <div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">แก้ไขปีงบประมาณ ITA</h1>
            <p class="mt-1 text-sm text-gray-500">
                แก้ไขชื่อหรือสถานะเปิดใช้งานของปีงบประมาณ
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('ita.fiscal-years.update', $fiscalYear) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('ita.fiscal-years.form', [
                    'fiscalYear' => $fiscalYear,
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
