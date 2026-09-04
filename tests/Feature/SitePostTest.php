<?php

use App\Models\SitePost;
use App\Models\SitePostFile;
use App\Models\SitePostImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

function postAuthor(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('a slug keeps Thai text instead of collapsing to nothing', function () {
    // Str::slug drops every character outside the latin range, which turns
    // "ประกาศวันหยุดสงกรานต์ 2569" into "2569" — and any other Thai title
    // ending in that year into the same thing.
    expect(SitePost::slugFor('ประกาศวันหยุดสงกรานต์ 2569'))->toBe('ประกาศวันหยุดสงกรานต์-2569')
        ->and(SitePost::slugFor('อบรม CPR ประจำปี'))->toBe('อบรม-cpr-ประจำปี')
        ->and(SitePost::slugFor('Health Fair 2026'))->toBe('health-fair-2026');
});

test('two posts with the same title get different slugs', function () {
    SitePost::factory()->create(['title' => 'ประกาศวันหยุด', 'slug' => SitePost::slugFor('ประกาศวันหยุด')]);

    expect(SitePost::slugFor('ประกาศวันหยุด'))->toBe('ประกาศวันหยุด-2');
});

test('a title with no usable characters still produces a slug', function () {
    expect(SitePost::slugFor('!!! ???'))->toStartWith('post-');
});

test('the news page is open to visitors', function () {
    $this->get(route('site.news'))->assertOk();
});

test('a draft never reaches a visitor', function () {
    $draft = SitePost::factory()->draft()->create(['title' => 'ยังไม่เผยแพร่']);

    $this->get(route('site.news'))->assertOk()->assertDontSee('ยังไม่เผยแพร่');
    $this->get(route('site.post', $draft->slug))->assertNotFound();
});

test('a post scheduled for later is not shown yet', function () {
    $scheduled = SitePost::factory()->scheduled()->create(['title' => 'ข่าวล่วงหน้า']);

    $this->get(route('site.news'))->assertOk()->assertDontSee('ข่าวล่วงหน้า');
    $this->get(route('site.post', $scheduled->slug))->assertNotFound();
});

test('a published post is readable', function () {
    $post = SitePost::factory()->create(['title' => 'เปิดบริการคลินิกใหม่', 'body' => 'รายละเอียดบริการ']);

    $this->get(route('site.post', $post->slug))
        ->assertOk()
        ->assertSee('เปิดบริการคลินิกใหม่')
        ->assertSee('รายละเอียดบริการ');
});

test('a pinned post comes first', function () {
    SitePost::factory()->create(['title' => 'ข่าวใหม่กว่า', 'published_at' => now()->subHour()]);
    SitePost::factory()->pinned()->create(['title' => 'ข่าวปักหมุด', 'published_at' => now()->subWeek()]);

    $html = $this->get(route('site.news'))->assertOk()->getContent();

    // Pinned wins over recency, which is the whole point of pinning.
    expect(strpos($html, 'ข่าวปักหมุด'))->toBeLessThan(strpos($html, 'ข่าวใหม่กว่า'));
});

test('the category filter narrows the list', function () {
    SitePost::factory()->category('news')->create(['title' => 'เรื่องข่าว']);
    SitePost::factory()->category('knowledge')->create(['title' => 'เรื่องความรู้']);

    $this->get(route('site.news', ['category' => 'news']))
        ->assertOk()
        ->assertSee('เรื่องข่าว')
        ->assertDontSee('เรื่องความรู้');
});

test('an unknown category shows everything rather than nothing', function () {
    SitePost::factory()->create(['title' => 'เรื่องที่มีอยู่']);

    $this->get(route('site.news', ['category' => 'made-up']))
        ->assertOk()
        ->assertSee('เรื่องที่มีอยู่');
});

test('post content is escaped rather than rendered as markup', function () {
    $post = SitePost::factory()->create(['body' => 'เนื้อหา <script>alert(1)</script> ต่อ']);

    $html = $this->get(route('site.post', $post->slug))->assertOk()->getContent();

    expect($html)->not->toContain('<script>alert(1)</script>')
        ->and($html)->toContain('&lt;script&gt;');
});

test('reading a post counts the view', function () {
    $post = SitePost::factory()->create(['title' => 'ข่าวที่มีคนอ่าน']);

    $this->get(route('site.post', $post->slug))->assertOk();

    // Deferred so the reader does not wait on the write; the test framework
    // runs deferred callbacks when the response is sent.
    expect($post->refresh()->view_count)->toBe(1);
});

test('counting a view does not look like an edit', function () {
    $post = SitePost::factory()->create();
    $before = $post->updated_at;

    $this->get(route('site.post', $post->slug))->assertOk();

    expect($post->refresh()->updated_at->timestamp)->toBe($before->timestamp);
});

test('the gallery shows activity posts only', function () {
    $activity = SitePost::factory()->category('activity')->create(['title' => 'กิจกรรมวันเด็ก']);
    SitePostImage::factory()->create(['site_post_id' => $activity->id]);

    SitePost::factory()->category('news')->create(['title' => 'ข่าวธรรมดา']);

    $this->get(route('site.gallery'))
        ->assertOk()
        ->assertSee('กิจกรรมวันเด็ก')
        ->assertDontSee('ข่าวธรรมดา');
});

test('publishing without a time still appears', function () {
    // The public scope requires a published_at, so leaving it empty would
    // otherwise mark a post published and leave it invisible.
    $this->actingAs(postAuthor('site.manage'))
        ->post(route('site.posts.store'), [
            'category' => 'news',
            'title' => 'ข่าวด่วน',
            'is_published' => '1',
        ])
        ->assertRedirect();

    $post = SitePost::firstOrFail();

    expect($post->published_at)->not->toBeNull();

    $this->get(route('site.news'))->assertOk()->assertSee('ข่าวด่วน');
});

test('the slug survives a title change unless asked to follow it', function () {
    $post = SitePost::factory()->create(['title' => 'ชื่อเดิม', 'slug' => 'ชื่อเดิม']);

    $author = postAuthor('site.manage');

    // Links already shared keep working.
    $this->actingAs($author)->put(route('site.posts.update', $post), [
        'category' => $post->category,
        'title' => 'ชื่อใหม่',
    ]);

    expect($post->refresh()->slug)->toBe('ชื่อเดิม');

    $this->actingAs($author)->put(route('site.posts.update', $post), [
        'category' => $post->category,
        'title' => 'ชื่อใหม่',
        'regenerate_slug' => '1',
    ]);

    expect($post->refresh()->slug)->toBe('ชื่อใหม่');
});

test('gallery images are attached to the post', function () {
    Storage::fake('public');

    $this->actingAs(postAuthor('site.manage'))
        ->post(route('site.posts.store'), [
            'category' => 'activity',
            'title' => 'กิจกรรมประจำปี',
            'gallery_images' => [
                UploadedFile::fake()->image('one.jpg'),
                UploadedFile::fake()->image('two.jpg'),
            ],
        ])
        ->assertRedirect();

    $post = SitePost::firstOrFail();

    expect($post->images)->toHaveCount(2)
        ->and($post->images->pluck('sort_order')->all())->toBe([1, 2]);

    foreach ($post->images as $image) {
        Storage::disk('public')->assertExists($image->image_path);
    }
});

test('deleting one image leaves the rest alone', function () {
    Storage::fake('public');

    $post = SitePost::factory()->create();
    $first = SitePostImage::factory()->create(['site_post_id' => $post->id]);
    $second = SitePostImage::factory()->create(['site_post_id' => $post->id]);

    $this->actingAs(postAuthor('site.manage'))
        ->delete(route('site.posts.images.destroy', [$post, $first]))
        ->assertRedirect();

    expect(SitePostImage::whereKey($first->id)->exists())->toBeFalse()
        ->and(SitePostImage::whereKey($second->id)->exists())->toBeTrue();
});

test('an image cannot be deleted through a post it does not belong to', function () {
    $postA = SitePost::factory()->create();
    $postB = SitePost::factory()->create();
    $image = SitePostImage::factory()->create(['site_post_id' => $postB->id]);

    $this->actingAs(postAuthor('site.manage'))
        ->delete(route('site.posts.images.destroy', [$postA, $image]))
        ->assertNotFound();

    expect(SitePostImage::whereKey($image->id)->exists())->toBeTrue();
});

test('writing needs the manage permission', function () {
    $this->actingAs(postAuthor('site.view'))
        ->get(route('site.posts.create'))
        ->assertForbidden();
});

test('a document can be attached and downloaded', function () {
    Storage::fake('public');

    $this->actingAs(postAuthor('site.manage'))
        ->post(route('site.posts.store'), [
            'category' => 'news',
            'title' => 'ประกาศรับสมัครงาน',
            'is_published' => '1',
            'attachments' => [UploadedFile::fake()->create('ประกาศ.pdf', 400, 'application/pdf')],
        ])
        ->assertRedirect();

    $file = SitePostFile::firstOrFail();

    expect($file->file_original_name)->toBe('ประกาศ.pdf')
        ->and($file->file_extension)->toBe('pdf')
        ->and($file->file_size)->toBeGreaterThan(0);

    Storage::disk('public')->assertExists($file->file_path);

    $response = $this->get(route('site.post.file', $file))->assertOk();

    $disposition = $response->headers->get('content-disposition');

    // Thai transliterates to nothing, so without a fallback the header
    // would offer a file called ".pdf" to anything not reading filename*.
    expect($disposition)->toContain("filename*=utf-8''")
        ->and($disposition)->toContain(rawurlencode('ประกาศ.pdf'))
        ->and($disposition)->toMatch('/filename="?document-\d+\.pdf"?/');
});

test('an attachment on a draft cannot be downloaded', function () {
    Storage::fake('public');

    $draft = SitePost::factory()->draft()->create();
    $file = SitePostFile::factory()->create(['site_post_id' => $draft->id]);

    // The file sits on the public disk, so this is what stops someone reaching
    // a document that has not been published yet.
    $this->get(route('site.post.file', $file))->assertNotFound();
});

test('an attachment on a post scheduled for later cannot be downloaded', function () {
    Storage::fake('public');

    $scheduled = SitePost::factory()->scheduled()->create();
    $file = SitePostFile::factory()->create(['site_post_id' => $scheduled->id]);

    $this->get(route('site.post.file', $file))->assertNotFound();
});

test('downloading counts the download', function () {
    Storage::fake('public');

    $post = SitePost::factory()->create();
    $file = SitePostFile::factory()->create(['site_post_id' => $post->id]);

    Storage::disk('public')->put($file->file_path, 'contents');

    $this->get(route('site.post.file', $file))->assertOk();

    expect($file->refresh()->download_count)->toBe(1);
});

test('a missing file is a 404 rather than a broken download', function () {
    Storage::fake('public');

    $post = SitePost::factory()->create();
    $file = SitePostFile::factory()->create(['site_post_id' => $post->id]);

    // The row exists but the file behind it does not.
    $this->get(route('site.post.file', $file))->assertNotFound();
});

test('an attachment cannot be deleted through a post it does not belong to', function () {
    $postA = SitePost::factory()->create();
    $postB = SitePost::factory()->create();
    $file = SitePostFile::factory()->create(['site_post_id' => $postB->id]);

    $this->actingAs(postAuthor('site.manage'))
        ->delete(route('site.posts.files.destroy', [$postA, $file]))
        ->assertNotFound();

    expect(SitePostFile::whereKey($file->id)->exists())->toBeTrue();
});

test('an executable is rejected as an attachment', function () {
    Storage::fake('public');

    $this->actingAs(postAuthor('site.manage'))
        ->post(route('site.posts.store'), [
            'category' => 'news',
            'title' => 'ทดสอบ',
            'attachments' => [UploadedFile::fake()->create('payload.exe', 10, 'application/octet-stream')],
        ])
        ->assertSessionHasErrors('attachments.0');

    expect(SitePost::count())->toBe(0);
});

test('the post page lists its attachments', function () {
    $post = SitePost::factory()->create(['title' => 'ข่าวมีเอกสาร']);

    SitePostFile::factory()->create([
        'site_post_id' => $post->id,
        'title' => 'แบบฟอร์มสมัครงาน',
        'file_size' => 2 * 1048576,
    ]);

    $this->get(route('site.post', $post->slug))
        ->assertOk()
        ->assertSee('เอกสารแนบ')
        ->assertSee('แบบฟอร์มสมัครงาน')
        ->assertSee('2.00 MB');
});

test('file sizes read the same everywhere', function () {
    // Three models displayed sizes and each had grown its own copy, which had
    // already drifted on small files.
    expect(human_file_size(512))->toBe('512 B')
        ->and(human_file_size(2048))->toBe('2.00 KB')
        ->and(human_file_size(3 * 1048576))->toBe('3.00 MB')
        ->and(human_file_size(0))->toBe('0 B')
        ->and(human_file_size(null))->toBe('0 B');
});
