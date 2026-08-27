<?php

use Illuminate\Support\Carbon;

beforeEach(function () {
    Carbon::setTestNow(Carbon::create(2026, 8, 27));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('years convert between the two calendars', function () {
    expect(thai_year(2026))->toBe(2569)
        ->and(christian_year(2569))->toBe(2026)
        ->and(christian_year(thai_year(2026)))->toBe(2026);
});

test('a buddhist year passes through untouched', function () {
    expect(normalize_thai_year(2569))->toBe(2569);
});

test('a gregorian year is promoted to buddhist', function () {
    expect(normalize_thai_year(2026))->toBe(2569);
});

test('an implausible or missing year falls back to this year', function () {
    expect(normalize_thai_year(null))->toBe(2569)
        ->and(normalize_thai_year(''))->toBe(2569)
        ->and(normalize_thai_year(0))->toBe(2569)
        ->and(normalize_thai_year(9999))->toBe(2569)
        ->and(normalize_thai_year('ไม่ใช่ปี'))->toBe(2569);
});

test('months outside 1-12 fall back to this month', function () {
    expect(normalize_month(3))->toBe(3)
        ->and(normalize_month(0))->toBe(8)
        ->and(normalize_month(13))->toBe(8)
        ->and(normalize_month(null))->toBe(8);
});

test('the month filter resolves to a gregorian date range', function () {
    $filter = resolve_month_filter(2, 2567);

    expect($filter['month'])->toBe(2)
        ->and($filter['year'])->toBe(2567)
        ->and($filter['start_date'])->toBe('2024-02-01')
        // 2024 was a leap year, so the range must end on the 29th.
        ->and($filter['end_date'])->toBe('2024-02-29');
});

test('the month filter accepts a gregorian year too', function () {
    $filter = resolve_month_filter(12, 2026);

    expect($filter['year'])->toBe(2569)
        ->and($filter['start_date'])->toBe('2026-12-01')
        ->and($filter['end_date'])->toBe('2026-12-31');
});
