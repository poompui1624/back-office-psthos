<x-layouts.app title="ไฟล์ ITA">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">ระบบรับไฟล์ ITA</h1>
                <p class="mt-1 text-sm text-gray-500">
                    แสดงไฟล์ที่อัปโหลดแล้ว เปิดดู แก้ไข ลบ และคัดลอกลิงก์ไฟล์
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('ita.public.index') }}"
                target="_blank"
                class="rounded bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">
                    หน้าแสดงผล
                </a>

                @can('ita.topic.manage')
                    <a href="{{ route('ita.fiscal-years.index') }}"
                    class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                        ปีงบประมาณ
                    </a>

                    <a href="{{ route('ita.moit-topics.index') }}"
                    class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                        หัวข้อหลัก
                    </a>

                    <a href="{{ route('ita.moit-sub-topics.index') }}"
                    class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                        หัวข้อย่อย
                    </a>
                @endcan

                @can('ita.create')
                    <a href="{{ route('ita.documents.create') }}"
                    class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        อัปโหลดไฟล์
                    </a>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <form method="GET" class="grid gap-3 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">ปีงบประมาณ</label>
                    <select name="fiscal_year_id" class="w-full rounded border-gray-300">
                        <option value="">-- ทุกปีงบประมาณ --</option>
                        @foreach ($fiscalYears as $year)
                            <option value="{{ $year->id }}" @selected((int) $selectedYearId === $year->id)>
                                {{ $year->year }}
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
                           placeholder="ชื่อไฟล์ / ชื่อเอกสาร">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                        ค้นหา
                    </button>

                    <a href="{{ route('ita.documents.index') }}"
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
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">หัวข้อย่อย</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">ไฟล์</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">ขนาด</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">สถานะ</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($documents as $document)
                            <tr>
                                <td class="px-4 py-3">
                                    {{ $document->fiscalYear?->year }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">
                                        {{ $document->mainTopic?->code }}
                                    </div>
                                    <div class="max-w-xs truncate text-gray-500">
                                        {{ $document->mainTopic?->title }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    @if ($document->subTopic)
                                        <div class="font-medium text-gray-900">
                                            {{ $document->subTopic->code }}
                                        </div>
                                        <div class="max-w-xs truncate text-gray-500">
                                            {{ $document->subTopic->title }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">
                                        {{ $document->title }}
                                    </div>
                                    <div class="text-gray-500">
                                        {{ $document->file_original_name }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $document->file_size_human }}
                                </td>

                                <td class="px-4 py-3">
                                    @if ($document->is_public)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700">
                                            เผยแพร่
                                        </span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600">
                                            ซ่อน
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ $document->file_url }}"
                                           target="_blank"
                                           class="rounded bg-gray-100 px-3 py-1 text-xs text-gray-700 hover:bg-gray-200">
                                            เปิดดู
                                        </a>

                                        <button type="button"
                                                onclick="copyToClipboard('{{ $document->file_url }}')"
                                                class="rounded bg-blue-100 px-3 py-1 text-xs text-blue-700 hover:bg-blue-200">
                                            คัดลอกลิงก์
                                        </button>

                                        @can('ita.edit')
                                            <a href="{{ route('ita.documents.edit', $document) }}"
                                               class="rounded bg-yellow-100 px-3 py-1 text-xs text-yellow-700 hover:bg-yellow-200">
                                                แก้ไข
                                            </a>
                                        @endcan

                                        @can('ita.delete')
                                            <form method="POST"
                                                  action="{{ route('ita.documents.destroy', $document) }}"
                                                  onsubmit="return confirm('ยืนยันการลบไฟล์นี้?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded bg-red-100 px-3 py-1 text-xs text-red-700 hover:bg-red-200">
                                                    ลบ
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    ยังไม่มีไฟล์ ITA
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-4 py-3">
                {{ $documents->links() }}
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function () {
                alert('คัดลอกลิงก์ไฟล์แล้ว');
            });
        }
    </script>
</x-layouts.app>
