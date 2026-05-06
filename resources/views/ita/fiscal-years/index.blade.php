<x-layouts.app title="จัดการปีงบประมาณ ITA">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">จัดการปีงบประมาณ ITA</h1>
                <p class="mt-1 text-sm text-gray-500">
                    เพิ่ม แก้ไข เปิด/ปิด ปีงบประมาณ สำหรับระบบ ITA / MOIT
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('ita.documents.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">
                    กลับไฟล์ ITA
                </a>

                <a href="{{ route('ita.moit-topics.index') }}"
                   class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                    หัวข้อหลัก
                </a>

                <a href="{{ route('ita.moit-sub-topics.index') }}"
                   class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                    หัวข้อย่อย
                </a>

                <a href="{{ route('ita.fiscal-years.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    เพิ่มปีงบประมาณ
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">ปีงบประมาณ</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">ชื่อ</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">หัวข้อหลัก</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">หัวข้อย่อย</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">ไฟล์</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">สถานะ</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($fiscalYears as $fiscalYear)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    {{ $fiscalYear->year }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $fiscalYear->name }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $fiscalYear->topics_count }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $fiscalYear->sub_topics_count }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $fiscalYear->documents_count }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($fiscalYear->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">
                                            ใช้งาน
                                        </span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600">
                                            ปิด
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('ita.moit-topics.index', ['fiscal_year_id' => $fiscalYear->id]) }}"
                                           class="rounded bg-blue-100 px-3 py-1 text-xs text-blue-700 hover:bg-blue-200">
                                            หัวข้อ
                                        </a>

                                        <a href="{{ route('ita.fiscal-years.edit', $fiscalYear) }}"
                                           class="rounded bg-yellow-100 px-3 py-1 text-xs text-yellow-700 hover:bg-yellow-200">
                                            แก้ไข
                                        </a>

                                        <form method="POST"
                                              action="{{ route('ita.fiscal-years.destroy', $fiscalYear) }}"
                                              onsubmit="return confirm('ยืนยันการลบปีงบประมาณนี้?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded bg-red-100 px-3 py-1 text-xs text-red-700 hover:bg-red-200">
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    ยังไม่มีปีงบประมาณ ITA
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">
                {{ $fiscalYears->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
