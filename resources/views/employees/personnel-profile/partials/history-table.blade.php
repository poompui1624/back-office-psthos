<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4 flex items-center justify-between gap-3">
        <h2 class="text-lg font-bold text-slate-900">{{ $title }}</h2>
        <span class="text-xs font-medium text-slate-500">กรอกแถวที่ต้องการ ระบบจะข้ามแถวว่าง</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    @foreach ($columns as $label)
                        <th class="px-3 py-2 text-left font-semibold">{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $rowIndex => $row)
                    <tr class="border-t border-slate-100">
                        @foreach ($columns as $key => $label)
                            <td class="p-2">
                                <input name="{{ $name }}[{{ $rowIndex }}][{{ $key }}]"
                                       value="{{ $row[$key] ?? '' }}"
                                       class="w-full rounded-lg border-slate-200">
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
