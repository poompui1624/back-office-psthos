@php
    $labels = [
        'pending' => ['รออนุมัติ', 'bg-amber-100 text-amber-800'],
        'approved' => ['อนุมัติแล้ว', 'bg-emerald-100 text-emerald-800'],
        'rejected' => ['ไม่อนุมัติ', 'bg-rose-100 text-rose-800'],
        'cancelled' => ['ยกเลิก', 'bg-slate-200 text-slate-700'],
        'assigned' => ['จัดแล้ว', 'bg-sky-100 text-sky-800'],
        'confirmed' => ['ยืนยันแล้ว', 'bg-emerald-100 text-emerald-800'],
        'in_progress' => ['กำลังดำเนินการ', 'bg-sky-100 text-sky-800'],
        'completed' => ['เสร็จสิ้น', 'bg-emerald-100 text-emerald-800'],
        'draft' => ['ฉบับร่าง', 'bg-slate-200 text-slate-700'],
    ];

    [$label, $classes] = $labels[$status] ?? [$status, 'bg-slate-200 text-slate-700'];
@endphp

<span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $classes }}">{{ $label }}</span>
