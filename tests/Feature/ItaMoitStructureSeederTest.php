<?php

use App\Models\ItaDocument;
use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use App\Models\User;
use Database\Seeders\ItaMoitStructureSeeder;

test('the seeder loads the full MOIT structure', function () {
    $this->seed(ItaMoitStructureSeeder::class);

    $year = ItaFiscalYear::where('year', 2569)->firstOrFail();

    expect(ItaMoitTopic::where('fiscal_year_id', $year->id)->count())->toBe(22)
        ->and(ItaMoitSubTopic::where('fiscal_year_id', $year->id)->count())->toBe(234);
});

test('items sort by their number rather than as text', function () {
    // Sorting the codes as strings puts 10 and 11 between 1.8 and 2, which is
    // how the master data comes out of a plain ORDER BY.
    $ordered = collect(['1', '1.1', '1.8', '2', '6.2.1', '10', '18', '18.4'])
        ->sortBy(fn (string $code) => ItaMoitStructureSeeder::sortOrderFor($code))
        ->values()
        ->all();

    expect($ordered)->toBe(['1', '1.1', '1.8', '2', '6.2.1', '10', '18', '18.4']);
});

test('MOIT2 items come back in the right order', function () {
    $this->seed(ItaMoitStructureSeeder::class);

    $codes = ItaMoitSubTopic::whereRelation('mainTopic', 'code', 'MOIT2')
        ->orderBy('sort_order')
        ->pluck('code')
        ->take(12)
        ->all();

    expect($codes)->toBe(['1', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '2', '3', '4']);
});

test('running it twice does not duplicate anything', function () {
    $this->seed(ItaMoitStructureSeeder::class);
    $this->seed(ItaMoitStructureSeeder::class);

    expect(ItaMoitTopic::count())->toBe(22)
        ->and(ItaMoitSubTopic::count())->toBe(234);
});

test('a topic dropped from the source is removed', function () {
    $this->seed(ItaMoitStructureSeeder::class);

    $year = ItaFiscalYear::where('year', 2569)->firstOrFail();

    $stale = ItaMoitTopic::create([
        'fiscal_year_id' => $year->id,
        'indicator_no' => 9,
        'code' => 'MOIT99',
        'title' => 'หัวข้อที่ไม่มีแล้ว',
        'sort_order' => 99,
        'is_active' => true,
    ]);

    $this->seed(ItaMoitStructureSeeder::class);

    expect(ItaMoitTopic::whereKey($stale->id)->exists())->toBeFalse()
        ->and(ItaMoitTopic::count())->toBe(22);
});

test('documents filed under a removed topic are kept and moved, not deleted', function () {
    $year = ItaFiscalYear::create(['year' => 2569, 'name' => 'ปีงบประมาณ 2569', 'is_active' => true]);

    $oldTopic = ItaMoitTopic::create([
        'fiscal_year_id' => $year->id, 'indicator_no' => 1, 'code' => 'MOIT 3',
        'title' => 'หัวข้อทดสอบ', 'sort_order' => 1, 'is_active' => true,
    ]);

    $oldSubTopic = ItaMoitSubTopic::create([
        'fiscal_year_id' => $year->id, 'main_topic_id' => $oldTopic->id,
        'code' => '3.1', 'title' => 'ข้อทดสอบ', 'sort_order' => 1, 'is_active' => true,
    ]);

    $document = ItaDocument::create([
        'fiscal_year_id' => $year->id,
        'main_topic_id' => $oldTopic->id,
        'sub_topic_id' => $oldSubTopic->id,
        'title' => 'ทดสอบระบบ',
        'file_original_name' => 'ทดสอบระบบ.pdf',
        'file_path' => 'ita/2569/test.pdf',
        'file_mime' => 'application/pdf',
        'file_extension' => 'pdf',
        'file_size' => 29406,
        'uploaded_by' => User::factory()->create()->id,
        'is_public' => true,
    ]);

    $this->seed(ItaMoitStructureSeeder::class);

    $document->refresh();

    // The upload outlives the structure it was filed under.
    expect(ItaDocument::whereKey($document->id)->exists())->toBeTrue()
        ->and($document->file_path)->toBe('ita/2569/test.pdf')
        ->and($document->main_topic_id)->not->toBe($oldTopic->id)
        ->and(ItaMoitTopic::whereKey($document->main_topic_id)->value('code'))->toBe('MOIT1');
});
