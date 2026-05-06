<x-layouts.app title="จัดการหัวข้อหลัก MOIT">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">จัดการหัวข้อหลัก MOIT</h1>
                <p class="mt-1 text-sm text-gray-500">
                    เพิ่ม แก้ไข ปิดใช้งาน หรือจัดเรียงหัวข้อหลักตามปีงบประมาณ
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('ita.documents.index') }}"
                   class="rounded bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">
                    กลับไฟล์ ITA
                </a>

                <a href="{{ route('ita.moit-sub-topics.index') }}"
                   class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                    หัวข้อย่อย
                </a>

                <a href="{{ route('ita.moit-topics.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    เพิ่มหัวข้อหลัก
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

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <form method="GET" class="grid gap-3 md:grid-cols-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ปีงบประมาณ</label>
                    <select name="fiscal_year_id" class="w-full rounded border-gray-300">
                        <option value="">-- ทุกปี --</option>
                        @foreach ($fiscalYears as $year)
                            <option value="{{ $year->id }}" @selected(request('fiscal_year_id') == $year->id)>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">ค้นหา</label>
                    <input type="text"
                           name="keyword"
                           value="{{ request('keyword') }}"
                           class="w-full rounded border-gray-300"
                           placeholder="MOIT / ชื่อหัวข้อ / ตัวชี้วัด">
                </div>

                <div class="flex items-end gap-2">
                    <button class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                        ค้นหา
                    </button>

                    <a href="{{ route('ita.moit-topics.index') }}"
                       class="rounded bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">
                        ล้าง
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">ปี</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">ตัวชี้วัด</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">MOIT</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">หัวข้อหลัก</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">หัวข้อย่อย</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">ไฟล์</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">สถานะ</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($topics as $topic)
                            <tr>
                                <td class="px-4 py-3">{{ $topic->fiscalYear?->year }}</td>
                                <td class="px-4 py-3">
                                    ตัวชี้วัดที่ {{ $topic->indicator_no }}
                                    <div class="text-gray-500">{{ $topic->indicator_title }}</div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $topic->code }}</td>
                                <td class="px-4 py-3">
                                    <div class="max-w-xl">{{ $topic->title }}</div>
                                    <div class="text-xs text-gray-400">ลำดับ: {{ $topic->sort_order }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ $topic->sub_topics_count }}</td>
                                <td class="px-4 py-3 text-center">{{ $topic->documents_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($topic->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">ใช้งาน</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600">ปิด</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('ita.moit-sub-topics.index', ['main_topic_id' => $topic->id]) }}"
                                           class="rounded bg-blue-100 px-3 py-1 text-xs text-blue-700 hover:bg-blue-200">
                                            หัวข้อย่อย
                                        </a>

                                        <a href="{{ route('ita.moit-topics.edit', $topic) }}"
                                           class="rounded bg-yellow-100 px-3 py-1 text-xs text-yellow-700 hover:bg-yellow-200">
                                            แก้ไข
                                        </a>

                                        <form method="POST"
                                              action="{{ route('ita.moit-topics.destroy', $topic) }}"
                                              onsubmit="return confirm('ยืนยันการลบหัวข้อหลักนี้?')">
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
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    ยังไม่มีหัวข้อหลัก MOIT
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
