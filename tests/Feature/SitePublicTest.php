<?php

use App\Models\SiteBanner;
use App\Models\SiteExecutive;
use App\Models\SiteLink;
use App\Models\SitePage;

test('the homepage is open to visitors', function () {
    // A public site behind a login is a failure nobody notices until someone
    // from outside cannot open it.
    $this->get(route('site.home'))->assertOk();
});

test('an information page is open to visitors', function () {
    SitePage::factory()->create(['key' => 'history', 'title' => 'ประวัติโรงพยาบาล', 'body' => 'ก่อตั้งเมื่อ พ.ศ. 2520']);

    $this->get(route('site.page', 'history'))
        ->assertOk()
        ->assertSee('ก่อตั้งเมื่อ พ.ศ. 2520');
});

test('a key the layout does not know is a 404, not a blank page', function () {
    $this->get(route('site.page', 'nonsense'))->assertNotFound();
});

test('an inactive page is not reachable', function () {
    SitePage::factory()->create(['key' => 'vision', 'is_active' => false]);

    $this->get(route('site.page', 'vision'))->assertNotFound();
});

test('banners appear in their sort order', function () {
    SiteBanner::factory()->create(['title' => 'แบนเนอร์ที่สาม', 'sort_order' => 3]);
    SiteBanner::factory()->create(['title' => 'แบนเนอร์ที่หนึ่ง', 'sort_order' => 1]);
    SiteBanner::factory()->create(['title' => 'แบนเนอร์ที่สอง', 'sort_order' => 2]);

    $html = $this->get(route('site.home'))->assertOk()->getContent();

    expect(strpos($html, 'แบนเนอร์ที่หนึ่ง'))
        ->toBeLessThan(strpos($html, 'แบนเนอร์ที่สอง'))
        ->and(strpos($html, 'แบนเนอร์ที่สอง'))
        ->toBeLessThan(strpos($html, 'แบนเนอร์ที่สาม'));
});

test('a banner outside its dates is not shown', function () {
    SiteBanner::factory()->create(['title' => 'แบนเนอร์ปัจจุบัน']);
    SiteBanner::factory()->expired()->create(['title' => 'แบนเนอร์หมดอายุ']);
    SiteBanner::factory()->scheduled()->create(['title' => 'แบนเนอร์ล่วงหน้า']);

    $this->get(route('site.home'))
        ->assertOk()
        ->assertSee('แบนเนอร์ปัจจุบัน')
        ->assertDontSee('แบนเนอร์หมดอายุ')
        ->assertDontSee('แบนเนอร์ล่วงหน้า');
});

test('an inactive banner is not shown even inside its dates', function () {
    SiteBanner::factory()->create(['title' => 'แบนเนอร์ปิดอยู่', 'is_active' => false]);

    $this->get(route('site.home'))->assertOk()->assertDontSee('แบนเนอร์ปิดอยู่');
});

test('only active links are shown', function () {
    SiteLink::factory()->create(['label' => 'ลิงก์ที่เปิด']);
    SiteLink::factory()->create(['label' => 'ลิงก์ที่ปิด', 'is_active' => false]);

    $this->get(route('site.home'))
        ->assertOk()
        ->assertSee('ลิงก์ที่เปิด')
        ->assertDontSee('ลิงก์ที่ปิด');
});

test('the featured executive appears on the homepage', function () {
    SiteExecutive::factory()->featured()->create(['name' => 'นายแพทย์สมชาย']);
    SiteExecutive::factory()->create(['name' => 'นางสาวสมหญิง']);

    $this->get(route('site.home'))
        ->assertOk()
        ->assertSee('นายแพทย์สมชาย')
        ->assertDontSee('นางสาวสมหญิง');
});

test('an inactive executive is never featured on the homepage', function () {
    SiteExecutive::factory()->featured()->create(['name' => 'ผู้บริหารที่ซ่อนอยู่', 'is_active' => false]);

    $this->get(route('site.home'))->assertOk()->assertDontSee('ผู้บริหารที่ซ่อนอยู่');
});

test('the structure page lists every active executive', function () {
    SitePage::factory()->create(['key' => 'structure', 'title' => 'โครงสร้างผู้บริหาร', 'body' => 'ผังการบริหาร']);

    SiteExecutive::factory()->featured()->create(['name' => 'ผู้อำนวยการ ก']);
    SiteExecutive::factory()->create(['name' => 'รองผู้อำนวยการ ข']);
    SiteExecutive::factory()->create(['name' => 'ผู้บริหารที่ปิดอยู่', 'is_active' => false]);

    $this->get(route('site.page', 'structure'))
        ->assertOk()
        ->assertSee('ผู้อำนวยการ ก')
        ->assertSee('รองผู้อำนวยการ ข')
        ->assertDontSee('ผู้บริหารที่ปิดอยู่');
});

test('page content is escaped rather than rendered as markup', function () {
    SitePage::factory()->create([
        'key' => 'history',
        'body' => 'ประวัติ <script>alert(1)</script> ต่อ',
    ]);

    $html = $this->get(route('site.page', 'history'))->assertOk()->getContent();

    // The body is plain text by design; rendering it as HTML would put an
    // injection point on a page open to the whole internet.
    expect($html)->not->toContain('<script>alert(1)</script>')
        ->and($html)->toContain('&lt;script&gt;');
});
