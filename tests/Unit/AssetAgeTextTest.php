<?php

use App\Models\Asset;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class);

test('asset age text displays years and months from received date', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-10'));

    $asset = new Asset([
        'received_date' => '2023-04-10',
    ]);

    expect($asset->age_text)->toBe('3 ปี 2 เดือน');

    Carbon::setTestNow();
});

test('asset age text displays dash when received date is empty', function () {
    $asset = new Asset;

    expect($asset->age_text)->toBe('-');
});
