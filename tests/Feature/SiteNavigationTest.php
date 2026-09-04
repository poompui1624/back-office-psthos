<?php

use App\Models\SitePage;

/**
 * Just the desktop navigation, so a label that also appears in the page title
 * or in the page's own tabs is not mistaken for the nav entry.
 */
function desktopNav(string $html): string
{
    $start = strpos($html, 'data-site-nav');

    if ($start === false) {
        return '';
    }

    $end = strpos($html, '</nav>', $start);

    return substr($html, $start, $end - $start);
}

/**
 * The opening tag of the anchor or summary carrying the given label.
 */
function navElementFor(string $html, string $label): string
{
    $nav = desktopNav($html);
    $position = strpos($nav, $label);

    if ($position === false) {
        return '';
    }

    $anchor = strrpos(substr($nav, 0, $position), '<a ');
    $summary = strrpos(substr($nav, 0, $position), '<summary ');

    $start = max($anchor === false ? -1 : $anchor, $summary === false ? -1 : $summary);

    if ($start < 0) {
        return '';
    }

    $end = strpos($nav, '>', $start);

    return substr($nav, $start, $end - $start);
}

test('the top bar carries five entries, not one per page', function () {
    $html = $this->get(route('site.home'))->assertOk()->getContent();

    // Eight across the top ran into the hospital name on a laptop.
    expect(substr_count(desktopNav($html), 'data-nav-group'))->toBe(2);

    foreach (['หน้าแรก', 'ข่าวและกิจกรรม', 'เกี่ยวกับโรงพยาบาล', 'เอกสารเผยแพร่'] as $label) {
        expect($html)->toContain($label);
    }
});

test('the grouped pages are still one click away', function () {
    SitePage::factory()->create(['key' => 'history']);
    SitePage::factory()->create(['key' => 'vision']);
    SitePage::factory()->create(['key' => 'structure']);

    $html = $this->get(route('site.home'))->assertOk()->getContent();

    foreach (['history', 'vision', 'structure'] as $key) {
        expect($html)->toContain(e(route('site.page', $key)));
    }

    expect($html)->toContain(e(route('site.news')))
        ->and($html)->toContain(e(route('site.gallery')));
});

test('the group holding the current page is highlighted', function () {
    SitePage::factory()->create(['key' => 'vision', 'title' => 'วิสัยทัศน์ พันธกิจ']);

    $html = $this->get(route('site.page', 'vision'))->assertOk()->getContent();

    expect(navElementFor($html, 'เกี่ยวกับโรงพยาบาล'))->toContain('bg-brand-500')
        ->and(navElementFor($html, 'ข่าวและกิจกรรม'))->not->toContain('bg-brand-500');
});

test('only one entry in a group is marked current at a time', function (string $url, string $expected, string $notExpected) {
    $html = $this->get($url)->assertOk()->getContent();

    // Without checking the query string too, the "all news" entry and the
    // category entries would all light up together.
    expect(navElementFor($html, $expected))->toContain('bg-brand-50')
        ->and(navElementFor($html, $notExpected))->not->toContain('bg-brand-50');
})->with([
    [fn () => route('site.news'), 'ข่าวสารทั้งหมด', 'ข่าวประชาสัมพันธ์'],
    [fn () => route('site.news', ['category' => 'news']), 'ข่าวประชาสัมพันธ์', 'ข่าวสารทั้งหมด'],
    [fn () => route('site.news', ['category' => 'knowledge']), 'ความรู้สู่ประชาชน', 'ข่าวสารทั้งหมด'],
]);

test('the dropdowns work without JavaScript', function () {
    $html = $this->get(route('site.home'))->assertOk()->getContent();

    // <details> opens on its own; the script only adds closing behaviour, so a
    // visitor with scripts blocked still reaches every page.
    expect($html)->toContain('<details class="relative" data-nav-group');
});

test('the phone menu lists every page without nesting', function () {
    SitePage::factory()->create(['key' => 'history']);

    $html = $this->get(route('site.home'))->assertOk()->getContent();

    // The groups become headings there rather than a dropdown inside a
    // dropdown, which is awkward to tap.
    $mobileStart = strpos($html, 'lg:hidden');

    expect($mobileStart)->not->toBeFalse();

    $mobile = substr($html, $mobileStart);

    foreach (['ข่าวสารทั้งหมด', 'ภาพกิจกรรม', 'ประวัติโรงพยาบาล', 'เอกสารเผยแพร่'] as $label) {
        expect($mobile)->toContain($label);
    }
});
