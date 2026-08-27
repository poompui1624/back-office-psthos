<?php

use App\Services\DocumentNumberService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Schema::create('doc_number_samples', function (Blueprint $table) {
        $table->id();
        $table->string('doc_no')->unique();
        $table->softDeletes();
    });
});

function issue(string $prefix = 'LV20260827'): string
{
    $number = DocumentNumberService::next($prefix, 'doc_number_samples', 'doc_no');

    DB::table('doc_number_samples')->insert(['doc_no' => $number]);

    return $number;
}

test('the first number for a prefix starts at one', function () {
    expect(issue())->toBe('LV202608270001');
});

test('numbers increment in sequence', function () {
    expect([issue(), issue(), issue()])
        ->toBe(['LV202608270001', 'LV202608270002', 'LV202608270003']);
});

test('deleting a record never reissues a number that is still in use', function () {
    $first = issue();
    issue();
    issue();

    DB::table('doc_number_samples')->where('doc_no', $first)->delete();

    // The count-based generator this replaced counted rows, so dropping one made it
    // hand out 0003 a second time and collide with the live row holding that number.
    expect(issue())->toBe('LV202608270004')
        ->and(DB::table('doc_number_samples')->where('doc_no', 'LV202608270003')->count())->toBe(1);
});

test('a soft deleted row keeps its number reserved', function () {
    issue();
    $second = issue();

    // Soft deletes leave the row in place, and next() reads the raw table,
    // so the number stays taken.
    DB::table('doc_number_samples')
        ->where('doc_no', $second)
        ->update(['deleted_at' => now()]);

    expect(issue())->toBe('LV202608270003');
});

test('each prefix keeps its own sequence', function () {
    issue('LV20260827');
    issue('LV20260827');

    expect(issue('RP20260827'))->toBe('RP202608270001')
        ->and(issue('LV20260827'))->toBe('LV202608270003');
});

test('the sequence rolls over past four digits without truncating', function () {
    DB::table('doc_number_samples')->insert(['doc_no' => 'LV202608279999']);

    expect(issue())->toBe('LV2026082710000');
});
