<?php

use App\Models\SiteBanner;
use App\Models\SiteExecutive;
use App\Models\SiteLink;
use App\Models\SitePage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

function siteAdminUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('managing the site needs its own permission', function (string $route) {
    $this->actingAs(siteAdminUser('dashboard.view'))
        ->get(route($route))
        ->assertForbidden();
})->with([
    'site.banners.index',
    'site.links.index',
    'site.pages.index',
    'site.executives.index',
]);

test('site.view alone can read but not reach the edit forms', function () {
    $banner = SiteBanner::factory()->create();

    $viewer = siteAdminUser('site.view');

    $this->actingAs($viewer)->get(route('site.banners.index'))->assertOk();
    $this->actingAs($viewer)->get(route('site.banners.edit', $banner))->assertForbidden();
});

test('a banner is stored with its image', function () {
    Storage::fake('public');

    $this->actingAs(siteAdminUser('site.manage'))
        ->post(route('site.banners.store'), [
            'image' => UploadedFile::fake()->image('banner.jpg', 1920, 600),
            'title' => 'ยินดีต้อนรับ',
            'sort_order' => 2,
            'is_active' => '1',
        ])
        ->assertRedirect(route('site.banners.index'));

    $banner = SiteBanner::firstOrFail();

    expect($banner->title)->toBe('ยินดีต้อนรับ')
        ->and($banner->sort_order)->toBe(2)
        ->and($banner->is_active)->toBeTrue();

    Storage::disk('public')->assertExists($banner->image_path);
});

test('an oversized banner is rejected', function () {
    Storage::fake('public');

    // Nothing resizes the upload, so the cap is the only thing keeping a huge
    // file off the homepage.
    $this->actingAs(siteAdminUser('site.manage'))
        ->post(route('site.banners.store'), [
            'image' => UploadedFile::fake()->create('huge.jpg', 3000, 'image/jpeg'),
            'is_active' => '1',
        ])
        ->assertSessionHasErrors('image');

    expect(SiteBanner::count())->toBe(0);
});

test('an end date before the start date is rejected', function () {
    Storage::fake('public');

    $this->actingAs(siteAdminUser('site.manage'))
        ->post(route('site.banners.store'), [
            'image' => UploadedFile::fake()->image('banner.jpg'),
            'starts_at' => '2026-10-01T08:00',
            'ends_at' => '2026-09-01T08:00',
        ])
        ->assertSessionHasErrors('ends_at');
});

test('replacing a banner image removes the file it replaced', function () {
    Storage::fake('public');

    $manager = siteAdminUser('site.manage');

    $this->actingAs($manager)->post(route('site.banners.store'), [
        'image' => UploadedFile::fake()->image('first.jpg'),
        'is_active' => '1',
    ]);

    $banner = SiteBanner::firstOrFail();
    $original = $banner->image_path;

    $this->actingAs($manager)->put(route('site.banners.update', $banner), [
        'image' => UploadedFile::fake()->image('second.jpg'),
        'is_active' => '1',
    ]);

    // Left behind, these accumulate silently every time an editor swaps an image.
    Storage::disk('public')->assertMissing($original);
    Storage::disk('public')->assertExists($banner->refresh()->image_path);
});

test('deleting a banner removes its file too', function () {
    Storage::fake('public');

    $manager = siteAdminUser('site.manage');

    $this->actingAs($manager)->post(route('site.banners.store'), [
        'image' => UploadedFile::fake()->image('banner.jpg'),
        'is_active' => '1',
    ]);

    $banner = SiteBanner::firstOrFail();
    $path = $banner->image_path;

    $this->actingAs($manager)->delete(route('site.banners.destroy', $banner));

    Storage::disk('public')->assertMissing($path);
    expect(SiteBanner::count())->toBe(0);
});

test('unchecking a link flag is saved', function () {
    $link = SiteLink::factory()->create(['is_active' => true, 'opens_new_tab' => true]);

    // An unchecked box posts nothing at all, so the controller has to read both
    // as booleans rather than trusting the keys to be present.
    $this->actingAs(siteAdminUser('site.manage'))
        ->put(route('site.links.update', $link), [
            'label' => $link->label,
            'url' => $link->url,
        ])
        ->assertRedirect();

    $link->refresh();

    expect($link->is_active)->toBeFalse()
        ->and($link->opens_new_tab)->toBeFalse();
});

test('featuring an executive unfeatures the previous one', function () {
    $first = SiteExecutive::factory()->featured()->create(['name' => 'ผู้อำนวยการคนเดิม']);
    $second = SiteExecutive::factory()->create(['name' => 'ผู้อำนวยการคนใหม่']);

    $this->actingAs(siteAdminUser('site.manage'))
        ->put(route('site.executives.update', $second), [
            'name' => $second->name,
            'is_featured' => '1',
            'is_active' => '1',
        ])
        ->assertRedirect();

    expect($second->refresh()->is_featured)->toBeTrue()
        ->and($first->refresh()->is_featured)->toBeFalse()
        ->and(SiteExecutive::where('is_featured', true)->count())->toBe(1);
});

test('the rule holds however the row is written, not only through the form', function () {
    SiteExecutive::factory()->featured()->create();

    // Written directly, the way a seeder or a console command would.
    SiteExecutive::factory()->featured()->create();

    expect(SiteExecutive::where('is_featured', true)->count())->toBe(1);
});

test('a page can be edited but not created or deleted', function () {
    $page = SitePage::factory()->create(['key' => 'history']);

    $this->actingAs(siteAdminUser('site.manage'))
        ->put(route('site.pages.update', $page), [
            'title' => 'ประวัติโรงพยาบาลปางศิลาทอง',
            'body' => 'เนื้อหาใหม่',
            'is_active' => '1',
        ])
        ->assertRedirect(route('site.pages.index'));

    expect($page->refresh()->title)->toBe('ประวัติโรงพยาบาลปางศิลาทอง');

    // The homepage places these by key, so there is deliberately no route to
    // add one it would not know where to put, or to remove one it expects.
    expect(Route::has('site.pages.store'))->toBeFalse()
        ->and(Route::has('site.pages.destroy'))->toBeFalse();
});
