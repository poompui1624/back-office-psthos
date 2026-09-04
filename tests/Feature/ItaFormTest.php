<?php

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitTopic;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function itaUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('the sub-topic form keeps the ids its filtering script looks up', function () {
    $year = ItaFiscalYear::create(['year' => 2569, 'name' => 'ปีงบประมาณ 2569', 'is_active' => true]);

    ItaMoitTopic::create([
        'fiscal_year_id' => $year->id,
        'code' => 'MOIT 1',
        'indicator_no' => 1,
        'title' => 'การเปิดเผยข้อมูล',
        'sort_order' => 0,
        'is_active' => true,
    ]);

    // The inline script pairs the two selects with getElementById, so losing
    // either id silently stops the year from filtering the topic list — the
    // page would still render, which is all the smoke test can tell us.
    $this->actingAs(itaUser('ita.topic.manage'))
        ->get(route('ita.moit-sub-topics.create'))
        ->assertOk()
        ->assertSee('id="fiscal_year_id"', false)
        ->assertSee('id="main_topic_id"', false)
        ->assertSee('data-year="'.$year->id.'"', false)
        ->assertSee('getElementById(\'fiscal_year_id\')', false);
});

test('the fiscal year form posts the fields the controller validates', function () {
    $this->actingAs(itaUser('ita.topic.manage'))
        ->post(route('ita.fiscal-years.store'), [
            'year' => 2570,
            'name' => 'ปีงบประมาณ 2570',
            'is_active' => '1',
        ])
        ->assertRedirect();

    $created = ItaFiscalYear::where('year', 2570)->firstOrFail();

    expect($created->name)->toBe('ปีงบประมาณ 2570')
        ->and($created->is_active)->toBeTrue();
});

test('a MOIT topic saves every field the form collects', function () {
    $year = ItaFiscalYear::create(['year' => 2569, 'name' => 'ปีงบประมาณ 2569', 'is_active' => true]);

    $this->actingAs(itaUser('ita.topic.manage'))
        ->post(route('ita.moit-topics.store'), [
            'fiscal_year_id' => $year->id,
            'indicator_no' => 3,
            'indicator_title' => 'การจัดซื้อจัดจ้าง',
            'code' => 'MOIT 5',
            'sort_order' => 7,
            'title' => 'สรุปผลการจัดซื้อจัดจ้างรายเดือน',
            'description' => 'แนบแบบ สขร.1',
            'is_active' => '1',
        ])
        ->assertRedirect();

    $topic = ItaMoitTopic::where('code', 'MOIT 5')->firstOrFail();

    expect($topic->fiscal_year_id)->toBe($year->id)
        ->and($topic->indicator_no)->toBe(3)
        ->and($topic->indicator_title)->toBe('การจัดซื้อจัดจ้าง')
        ->and($topic->sort_order)->toBe(7)
        ->and($topic->title)->toBe('สรุปผลการจัดซื้อจัดจ้างรายเดือน')
        ->and($topic->description)->toBe('แนบแบบ สขร.1');
});
