<x-layouts.app title="Software Inventory">
    <x-page-header title="Software Inventory"
                   subtitle="ค้นหาว่าโปรแกรมใดติดตั้งอยู่บนเครื่องไหนบ้าง จากรายงานล่าสุดของ Agent" />

    <div class="mb-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="โปรแกรมที่ต่างกัน" :value="number_format($summary['products'])" icon="box" tone="brand" />
        <x-stat-card label="การติดตั้งรวม" :value="number_format($summary['installs'])" icon="clipboard" tone="violet" />
        <x-stat-card label="เครื่องที่รายงาน" :value="number_format($summary['computers'])" icon="device" tone="emerald" />
        <x-stat-card label="รายงานล่าสุด"
                     :value="$summary['last_report']?->diffForHumans() ?? 'ยังไม่มี'"
                     icon="clock" tone="amber" />
    </div>

    <x-filter-bar :action="route('software-inventory.index')">
        <x-form.field label="ค้นหาโปรแกรม" class="min-w-64 flex-1">
            <x-form.input name="search" :value="$search" placeholder="เช่น chrome, office, autocad" autofocus />
        </x-form.field>

        <x-form.field label="หน่วยงาน">
            <x-form.select name="department_id" class="w-44">
                <option value="">ทุกหน่วยงาน</option>

                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected($departmentId == $department->id)>
                        {{ $department->name }}
                    </option>
                @endforeach
            </x-form.select>
        </x-form.field>

        <x-form.field label="Publisher">
            <x-form.select name="publisher" class="w-48">
                <option value="">ทุก Publisher</option>

                @foreach ($publishers as $option)
                    <option value="{{ $option }}" @selected($publisher === $option)>{{ $option }}</option>
                @endforeach
            </x-form.select>
        </x-form.field>

        <x-form.checkbox name="include_components" value="1" :checked="$includeComponents"
                         class="pb-2" label="รวมส่วนประกอบระบบ" />
    </x-filter-bar>

    @if ($hasQuery)
        <x-data-table>
            <x-slot:head>
                <x-data-table.th>โปรแกรม</x-data-table.th>
                <x-data-table.th>Publisher</x-data-table.th>
                <x-data-table.th align="center">เวอร์ชัน</x-data-table.th>
                <x-data-table.th align="center">จำนวนเครื่อง</x-data-table.th>
                <x-data-table.th align="center">จัดการ</x-data-table.th>
            </x-slot:head>

            @forelse ($products as $product)
                <x-data-table.row>
                    <x-data-table.td class="font-medium text-slate-900">{{ $product->name }}</x-data-table.td>
                    <x-data-table.td>{{ $product->publisher ?: '-' }}</x-data-table.td>

                    <x-data-table.td align="center">
                        <x-badge tone="slate">{{ $product->version_count }}</x-badge>
                    </x-data-table.td>

                    <x-data-table.td align="center">
                        <x-badge tone="brand">{{ $product->computer_count }}</x-badge>
                    </x-data-table.td>

                    <x-data-table.td align="center">
                        <x-btn variant="secondary" size="sm"
                               :href="route('software-inventory.computers', ['name' => $product->name])">
                            ดูเครื่องที่ติดตั้ง
                        </x-btn>
                    </x-data-table.td>
                </x-data-table.row>
            @empty
                <x-data-table.empty :colspan="5" icon="box" title="ไม่พบโปรแกรมที่ตรงกับเงื่อนไข"
                                    description="ลองใช้คำค้นที่สั้นลง หรือติ๊ก 'รวมส่วนประกอบระบบ' หากกำลังหา driver หรือ runtime" />
            @endforelse
        </x-data-table>

        <div class="mt-4">{{ $products->links() }}</div>
    @else
        {{--
            The full list runs to a couple of thousand rows once a fleet is
            reporting, and this page exists for looking one thing up. The top
            ten give a sense of the data without becoming something to wade
            through.
        --}}
        <div class="card">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="section-title">ติดตั้งมากที่สุด</h2>
                <p class="muted mt-1">พิมพ์ในช่องค้นหาด้านบนเพื่อดูรายการทั้งหมด</p>
            </div>

            @if ($topProducts->isEmpty())
                <x-empty-state icon="box" title="ยังไม่มีข้อมูลซอฟต์แวร์"
                               description="ข้อมูลจะปรากฏเมื่อ Agent ส่งรายการโปรแกรมเข้ามา" />
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($topProducts as $product)
                        <li class="flex items-center justify-between gap-4 px-5 py-3">
                            <div class="min-w-0">
                                <div class="truncate font-medium text-slate-900">{{ $product->name }}</div>

                                <div class="mt-0.5 text-xs text-slate-500">
                                    {{ $product->publisher ?: 'ไม่ระบุ Publisher' }}

                                    @if ($product->version_count > 1)
                                        &middot; {{ $product->version_count }} เวอร์ชัน
                                    @endif
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-3">
                                <x-badge tone="brand">{{ $product->computer_count }} เครื่อง</x-badge>

                                <x-btn variant="ghost" size="sm"
                                       :href="route('software-inventory.computers', ['name' => $product->name])">
                                    ดู
                                </x-btn>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</x-layouts.app>
