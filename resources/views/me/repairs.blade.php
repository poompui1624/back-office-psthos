<x-layouts.app>
    @include('me._header', ['employee' => $employee, 'title' => 'แจ้งซ่อมของฉัน', 'subtitle' => 'รายการที่คุณแจ้งไว้'])

    @can('repair.create')
        <div class="mb-4">
            <a href="{{ route('repair-requests.create') }}"
               class="inline-block rounded-xl bg-[#02abff] px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                แจ้งซ่อมใหม่
            </a>
        </div>
    @endcan

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">เลขที่</th>
                        <th class="px-4 py-3 font-semibold">เรื่อง</th>
                        <th class="px-4 py-3 font-semibold">สถานที่</th>
                        <th class="px-4 py-3 font-semibold">ผู้รับผิดชอบ</th>
                        <th class="px-4 py-3 text-center font-semibold">สถานะ</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($repairRequests as $repair)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $repair->ticket_no }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $repair->title }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $repair->location ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $repair->assignedUser?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">@include('me._status', ['status' => $repair->status])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                คุณยังไม่เคยแจ้งซ่อม
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $repairRequests->links() }}</div>
</x-layouts.app>
