@php
    $months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม',
    ];
    $thisYear = thai_year((int) now()->year);
@endphp

<form method="GET" action="{{ route($route) }}" class="mb-4 flex flex-wrap gap-2 rounded-2xl bg-white p-4 shadow-sm">
    <select name="month" class="rounded-xl border-slate-300 text-sm">
        @foreach ($months as $value => $label)
            <option value="{{ $value }}" @selected($month === $value)>{{ $label }}</option>
        @endforeach
    </select>

    <select name="year" class="rounded-xl border-slate-300 text-sm">
        @foreach (range($thisYear - 3, $thisYear + 1) as $value)
            <option value="{{ $value }}" @selected($year === $value)>{{ $value }}</option>
        @endforeach
    </select>

    <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
        แสดง
    </button>
</form>
