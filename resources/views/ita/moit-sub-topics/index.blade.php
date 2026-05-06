<x-layouts.app title="จัดการหัวข้อย่อย MOIT">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">จัดการหัวข้อย่อย MOIT</h1>
                <p class="mt-1 text-sm text-gray-500">
                    เพิ่ม แก้ไข ปิดใช้งาน หรือจัดเรียงหัวข้อย่อยของแต่ละ MOIT
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('ita.moit-topics.index') }}"
                   class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                    หัวข้อหลัก
                </a>

                <a href="{{ route('ita.moit-sub-topics.create') }}"
                   class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    เพิ่มหัวข้อย่อย
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

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">หัวข้อหลัก</label>
                    <select name="main_topic_id" class="w-full rounded border-gray-300">
                        <option value="">-- ทุก MOIT --</option>
                        @foreach ($mainTopics as $topic)
                            <option value="{{ $topic->id }}" @selected(request('main_topic_id') == $topic->id)>
                                {{ $topic->code }} {{ Str::limit($topic->title, 40) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ค้นหา</label>
                    <input type="text"
                           name="keyword"
                           value="{{ request('keyword') }}"
                           class="w-full rounded border-gray-300"
                           placeholder="รหัส / ชื่อหัวข้อย่อย">
                </div>

                <div class="flex items-end gap-2">
                    <button class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                        ค้นหา
                    </button>

                    <a href="{{ route('ita.moit-sub-topics.index') }}"
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
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">MOIT</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">รหัส</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">หัวข้อย่อย</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">ไฟล์</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">สถานะ</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse ($subTopics as $subTopic)
                            <tr>
                                <td class="px-4 py-3">{{ $subTopic->fiscalYear?->year }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ $subTopic->mainTopic?->code }}</div>
                                    <div class="max-w-xs truncate text-gray-500">
                                        {{ $subTopic->mainTopic?->title }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $subTopic->code }}</td>
                                <td class="px-4 py-3">
                                    <div class="max-w-xl">{{ $subTopic->title }}</div>
                                    <div class="text-xs text-gray-400">ลำดับ: {{ $subTopic->sort_order }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ $subTopic->documents_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($subTopic->is_active)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">ใช้งาน</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600">ปิด</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('ita.moit-sub-topics.edit', $subTopic) }}"
                                           class="rounded bg-yellow-100 px-3 py-1 text-xs text-yellow-700 hover:bg-yellow-200">
                                            แก้ไข
                                        </a>

                                        <form method="POST"
                                              action="{{ route('ita.moit-sub-topics.destroy', $subTopic) }}"
                                              onsubmit="return confirm('ยืนยันการลบหัวข้อย่อยนี้?')">
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
                                    ยังไม่มีหัวข้อย่อย MOIT
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">
                {{ $subTopics->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
