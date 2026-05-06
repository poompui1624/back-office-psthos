<x-layouts.app title="เพิ่มหัวข้อหลัก MOIT">
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">เพิ่มหัวข้อหลัก MOIT</h1>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('ita.moit-topics.store') }}" class="space-y-5">
                @csrf

                @include('ita.moit-topics.form', [
                    'topic' => null,
                    'fiscalYears' => $fiscalYears,
                ])

                <div class="flex justify-end gap-2">
                    <a href="{{ route('ita.moit-topics.index') }}"
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
