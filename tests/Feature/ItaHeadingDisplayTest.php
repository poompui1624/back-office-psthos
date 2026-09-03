<?php

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function itaHeadingUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

function makeHeadingSubTopic(array $attributes = []): ItaMoitSubTopic
{
    $year = ItaFiscalYear::firstOrCreate(['year' => 2569], ['name' => 'ปีงบประมาณ 2569', 'is_active' => true]);

    $topic = ItaMoitTopic::firstOrCreate(
        ['fiscal_year_id' => $year->id, 'code' => 'MOIT4'],
        ['indicator_no' => 2, 'title' => 'หัวข้อทดสอบ', 'sort_order' => 1, 'is_active' => true]
    );

    return ItaMoitSubTopic::create(array_merge([
        'fiscal_year_id' => $year->id,
        'main_topic_id' => $topic->id,
        'code' => '3',
        'title' => 'ข้อ 3. รายงานผลของแผนการจัดซื้อจัดจ้าง',
        'is_heading' => true,
        'sort_order' => 30000,
        'is_active' => true,
    ], $attributes));
}

test('a heading is not struck through on the public page', function () {
    $heading = makeHeadingSubTopic();

    $html = $this->get(route('ita.public.index', 2569))->assertOk()->getContent();

    // Without a document a normal item is greyed out and struck through, which
    // is wrong for an item that will never carry one.
    $position = strpos($html, $heading->title);

    expect($position)->not->toBeFalse();

    $surrounding = substr($html, max(0, $position - 300), 400);

    expect($surrounding)->not->toContain('line-through');
});

test('a normal item with no document is marked as not yet published', function () {
    makeHeadingSubTopic(['code' => '3.1', 'title' => 'มีบันทึกข้อความรายงานผล', 'is_heading' => false]);

    $response = $this->get(route('ita.public.index', 2569))->assertOk();

    // This used to be struck through. On a public record a line through the
    // text reads as withdrawn rather than pending, so the state is now said in
    // words. The fixture holds a single item, so asserting against the whole
    // page is unambiguous — and does not depend on slicing raw HTML, where a
    // byte-counted window lands mid-tag once the text is Thai.
    $response->assertSeeText('ยังไม่เผยแพร่')
        ->assertDontSee('line-through');
});
test('the flag can be set when creating an item', function () {
    $existing = makeHeadingSubTopic();

    $this->actingAs(itaHeadingUser('ita.topic.manage'))
        ->post(route('ita.moit-sub-topics.store'), [
            'fiscal_year_id' => $existing->fiscal_year_id,
            'main_topic_id' => $existing->main_topic_id,
            'code' => '4',
            'title' => 'ข้อ 4. หัวข้อใหม่',
            'is_heading' => '1',
            'is_active' => '1',
        ])->assertRedirect();

    expect(ItaMoitSubTopic::where('code', '4')->value('is_heading'))->toBeTrue();
});

test('the flag can be turned off again', function () {
    $heading = makeHeadingSubTopic();

    // An unchecked box posts nothing, so the controller has to read it as a
    // boolean rather than trusting the key to be present.
    $this->actingAs(itaHeadingUser('ita.topic.manage'))
        ->put(route('ita.moit-sub-topics.update', $heading), [
            'fiscal_year_id' => $heading->fiscal_year_id,
            'main_topic_id' => $heading->main_topic_id,
            'code' => $heading->code,
            'title' => $heading->title,
            'is_active' => '1',
        ])->assertRedirect();

    expect($heading->refresh()->is_heading)->toBeFalse();
});

test('the admin list marks headings', function () {
    makeHeadingSubTopic();

    $this->actingAs(itaHeadingUser('ita.topic.manage'))
        ->get(route('ita.moit-sub-topics.index'))
        ->assertOk()
        ->assertSee('หัวข้อกลุ่ม');
});
