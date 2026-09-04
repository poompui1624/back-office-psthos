<?php

use App\Models\ItaDocument;
use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use App\Models\User;

function publicYear(int $year = 2569): ItaFiscalYear
{
    return ItaFiscalYear::firstOrCreate(
        ['year' => $year],
        ['name' => 'ปีงบประมาณ '.$year, 'is_active' => true]
    );
}

function publicTopic(ItaFiscalYear $year, string $code = 'MOIT4', int $indicator = 2): ItaMoitTopic
{
    return ItaMoitTopic::create([
        'fiscal_year_id' => $year->id,
        'indicator_no' => $indicator,
        'indicator_title' => 'ตัวชี้วัดที่ '.$indicator.' การจัดซื้อจัดจ้าง',
        'code' => $code,
        'title' => 'หน่วยงานมีการวางระบบการจัดซื้อจัดจ้าง',
        'sort_order' => 1,
        'is_active' => true,
    ]);
}

function publicItem(ItaMoitTopic $topic, string $code, bool $heading = false): ItaMoitSubTopic
{
    return ItaMoitSubTopic::create([
        'fiscal_year_id' => $topic->fiscal_year_id,
        'main_topic_id' => $topic->id,
        'code' => $code,
        'title' => 'รายการ '.$code,
        'is_heading' => $heading,
        'sort_order' => 1,
        'is_active' => true,
    ]);
}

function publishTo(ItaMoitSubTopic $item, string $title = 'เอกสารเผยแพร่'): ItaDocument
{
    return ItaDocument::create([
        'fiscal_year_id' => $item->fiscal_year_id,
        'main_topic_id' => $item->main_topic_id,
        'sub_topic_id' => $item->id,
        'title' => $title,
        'file_original_name' => $title.'.pdf',
        'file_path' => 'ita/2569/'.uniqid().'.pdf',
        'file_mime' => 'application/pdf',
        'file_extension' => 'pdf',
        'file_size' => 1024,
        'uploaded_by' => User::factory()->create()->id,
        'is_public' => true,
    ]);
}

test('the indicator heading is not printed twice', function () {
    $topic = publicTopic(publicYear());
    publicItem($topic, '1');

    // indicator_title already begins "ตัวชี้วัดที่ 2", and the page used to
    // prepend the number again.
    $html = $this->get(route('ita.public.index', 2569))->assertOk()->getContent();

    expect(substr_count($html, 'ตัวชี้วัดที่ 2 ตัวชี้วัดที่ 2'))->toBe(0)
        ->and($html)->toContain('ตัวชี้วัดที่ 2 การจัดซื้อจัดจ้าง');
});

test('progress counts published items and ignores headings', function () {
    $year = publicYear();
    $topic = publicTopic($year);

    publicItem($topic, '1', heading: true);
    $withFile = publicItem($topic, '1.1');
    publicItem($topic, '1.2');
    publicItem($topic, '1.3');

    publishTo($withFile);

    // Three real items, one published. The heading is not an item to publish.
    $this->get(route('ita.public.index', 2569))
        ->assertOk()
        ->assertSeeText('/ 3 รายการ')
        ->assertSeeText('33%');
});

test('a topic where everything is published is marked complete', function () {
    $year = publicYear();
    $topic = publicTopic($year);

    $first = publicItem($topic, '1.1');
    $second = publicItem($topic, '1.2');

    publishTo($first);
    publishTo($second);

    $this->get(route('ita.public.index', 2569))
        ->assertOk()
        ->assertSeeText('100%')
        ->assertSeeText('2/2');
});

test('an unpublished item says so instead of being struck through', function () {
    $topic = publicTopic(publicYear());
    publicItem($topic, '1.1');

    $html = $this->get(route('ita.public.index', 2569))->assertOk()->getContent();

    // A line through the text reads as withdrawn rather than pending, which is
    // the wrong thing to say on a public record.
    expect($html)->toContain('ยังไม่เผยแพร่')
        ->and($html)->not->toContain('line-through');
});

test('a heading carries no published or pending marker', function () {
    $topic = publicTopic(publicYear());
    $heading = publicItem($topic, '3', heading: true);

    $html = $this->get(route('ita.public.index', 2569))->assertOk()->getContent();

    $position = strpos($html, 'รายการ 3');
    $block = substr($html, max(0, $position - 400), 500);

    expect($block)->not->toContain('ยังไม่เผยแพร่');
});

test('only public documents are listed', function () {
    $topic = publicTopic(publicYear());
    $item = publicItem($topic, '1.1');

    $document = publishTo($item, 'เอกสารภายใน');
    $document->update(['is_public' => false]);

    $this->get(route('ita.public.index', 2569))
        ->assertOk()
        ->assertDontSee('เอกสารภายใน')
        ->assertSeeText('ยังไม่เผยแพร่');
});

test('an unknown fiscal year is a 404', function () {
    publicYear();

    $this->get(route('ita.public.index', 2999))->assertNotFound();
});

test('the page works with no topics at all', function () {
    publicYear();

    $this->get(route('ita.public.index', 2569))
        ->assertOk()
        ->assertSeeText('ยังไม่มีข้อมูล ITA สำหรับปีงบประมาณนี้');
});
