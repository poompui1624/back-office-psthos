<x-layouts.app>
    <div class="mx-auto max-w-2xl">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 sm:p-8">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-400 text-lg font-bold text-white">
                    !
                </div>

                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-amber-900 sm:text-xl">
                        บัญชีนี้ยังไม่ได้เชื่อมกับทะเบียนบุคลากร
                    </h1>

                    <p class="mt-2 text-sm leading-relaxed text-amber-800">
                        ระบบจึงยังไม่ทราบว่า &ldquo;ของฉัน&rdquo; หมายถึงข้อมูลของใคร
                        ทำให้ยังแสดงใบลา ตารางเวร สลิปเงินเดือน และการลงเวลาของคุณไม่ได้
                    </p>

                    <div class="mt-5 rounded-xl border border-amber-200 bg-white p-4">
                        <div class="text-sm font-semibold text-slate-900">วิธีแก้ไข</div>

                        <p class="mt-1 text-sm text-slate-600">
                            แจ้งผู้ดูแลระบบหรือฝ่ายบุคคล ให้เปิดหน้า
                            <span class="font-medium text-slate-900">ผู้ใช้งานระบบ</span>
                            แล้วผูกบัญชีนี้เข้ากับทะเบียนบุคลากรของคุณ
                        </p>

                        <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-slate-500">ชื่อบัญชี</dt>
                                <dd class="font-medium text-slate-900">{{ auth()->user()->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">อีเมล</dt>
                                <dd class="font-medium text-slate-900">{{ auth()->user()->email }}</dd>
                            </div>
                        </dl>
                    </div>

                    @can('user.update')
                        <a href="{{ route('users.edit', auth()->id()) }}"
                           class="mt-5 inline-block rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                            ผูกบัญชีเดี๋ยวนี้
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
