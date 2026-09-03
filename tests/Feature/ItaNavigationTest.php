<?php

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function itaNavUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

/**
 * The opening <a ...> tag of the nav link pointing at $href.
 */
function navAnchorFor(string $html, string $href): string
{
    $needle = 'href="'.e($href).'"';
    $position = strpos($html, $needle);

    if ($position === false) {
        return '';
    }

    $start = strrpos(substr($html, 0, $position), '<a ');
    $end = strpos($html, '>', $position);

    return substr($html, $start, $end - $start);
}
function itaNavFixtures(): ItaMoitSubTopic
{
    $year = ItaFiscalYear::create(['year' => 2569, 'name' => 'ปีงบประมาณ 2569', 'is_active' => true]);

    $topic = ItaMoitTopic::create([
        'fiscal_year_id' => $year->id, 'indicator_no' => 1, 'code' => 'MOIT1',
        'title' => 'หัวข้อทดสอบ', 'sort_order' => 1, 'is_active' => true,
    ]);

    return ItaMoitSubTopic::create([
        'fiscal_year_id' => $year->id, 'main_topic_id' => $topic->id,
        'code' => '1.1', 'title' => 'ข้อทดสอบ', 'sort_order' => 10100, 'is_active' => true,
    ]);
}

$adminPages = [
    'ita.documents.index',
    'ita.documents.create',
    'ita.fiscal-years.index',
    'ita.fiscal-years.create',
    'ita.moit-topics.index',
    'ita.moit-topics.create',
    'ita.moit-sub-topics.index',
    'ita.moit-sub-topics.create',
];

test('every ITA admin page carries the same navigation', function (string $route) {
    itaNavFixtures();

    $user = itaNavUser('ita.view', 'ita.create', 'ita.edit', 'ita.topic.manage');

    $this->actingAs($user)->get(route($route))
        ->assertOk()
        ->assertSee('ไฟล์ ITA')
        ->assertSee('ปีงบประมาณ')
        ->assertSee('หัวข้อหลัก')
        ->assertSee('หัวข้อย่อย')
        ->assertSee('หน้าแสดงผล');
})->with($adminPages);

test('the tab stays highlighted while creating inside a section', function () {
    itaNavFixtures();

    $user = itaNavUser('ita.view', 'ita.create', 'ita.topic.manage');

    // The upload form used to carry no navigation at all, so there was no way
    // back to the rest of the section without the browser's back button.
    $html = $this->actingAs($user)->get(route('ita.moit-sub-topics.create'))->assertOk()->getContent();

    // Locate the tab by its href rather than its label: the label also appears
    // in the page title, which is what a naive search finds first.
    expect(navAnchorFor($html, route('ita.moit-sub-topics.index')))->toContain('bg-slate-900')
        ->and(navAnchorFor($html, route('ita.moit-topics.index')))->not->toContain('bg-slate-900');
});

test('a user who can only view documents does not see the structure tabs', function () {
    itaNavFixtures();

    $this->actingAs(itaNavUser('ita.view'))
        ->get(route('ita.documents.index'))
        ->assertOk()
        ->assertSee('ไฟล์ ITA')
        ->assertDontSee('หัวข้อหลัก')
        ->assertDontSee('ปีงบประมาณ</a>', false);
});

test('the public page link opens in a new tab', function () {
    itaNavFixtures();

    $html = $this->actingAs(itaNavUser('ita.view'))
        ->get(route('ita.documents.index'))
        ->assertOk()
        ->getContent();

    $anchor = navAnchorFor($html, route('ita.public.index'));

    expect($anchor)->toContain('target="_blank"')
        ->and($anchor)->toContain('rel="noopener"');
});
