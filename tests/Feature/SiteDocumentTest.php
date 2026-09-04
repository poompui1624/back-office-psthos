<?php

use App\Models\SiteDocument;
use App\Models\User;
use App\Support\QrCode;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

function documentManager(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('the document register is open to visitors', function () {
    $this->get(route('site.documents'))->assertOk();
});

test('a draft document never reaches a visitor', function () {
    $draft = SiteDocument::factory()->draft()->create(['title' => 'ยังไม่เผยแพร่']);

    $this->get(route('site.documents'))->assertOk()->assertDontSee('ยังไม่เผยแพร่');
    $this->get(route('site.document', $draft))->assertNotFound();
    $this->get(route('site.document.download', $draft))->assertNotFound();
});

test('a document scheduled for later is not shown yet', function () {
    $scheduled = SiteDocument::factory()->scheduled()->create(['title' => 'ประกาศล่วงหน้า']);

    $this->get(route('site.documents'))->assertOk()->assertDontSee('ประกาศล่วงหน้า');
    $this->get(route('site.document', $scheduled))->assertNotFound();
});

test('the category filter narrows the register', function () {
    SiteDocument::factory()->category('procurement')->create(['title' => 'ประกาศประกวดราคา']);
    SiteDocument::factory()->category('job')->create(['title' => 'รับสมัครพยาบาล']);

    $this->get(route('site.documents', ['category' => 'procurement']))
        ->assertOk()
        ->assertSee('ประกาศประกวดราคา')
        ->assertDontSee('รับสมัครพยาบาล');
});

test('search narrows the register', function () {
    SiteDocument::factory()->create(['title' => 'ประกาศประกวดราคาครุภัณฑ์']);
    SiteDocument::factory()->create(['title' => 'รายงานประจำปี 2568']);

    $this->get(route('site.documents', ['search' => 'ประกวดราคา']))
        ->assertOk()
        ->assertSee('ประกาศประกวดราคาครุภัณฑ์')
        ->assertDontSee('รายงานประจำปี 2568');
});

test('a document page carries a QR code for the page itself', function () {
    $document = SiteDocument::factory()->create(['title' => 'ประกาศรับสมัครงาน']);

    $html = $this->get(route('site.document', $document))->assertOk()->getContent();

    // Printed onto a notice, this is how someone reaches the page from paper.
    expect($html)->toContain('<svg')
        ->and($html)->toContain('สแกนเพื่อเปิดหน้านี้');
});

test('the QR code is inline SVG with no second XML declaration', function () {
    $svg = QrCode::inline('https://example.test/x');

    // A second XML declaration inside a page is invalid and browsers
    // disagree on what to do with it. (Spelling it out rather than writing
    // the literal tag: in a // comment its closing bracket ends PHP mode.)
    expect($svg)->toStartWith('<svg')
        ->and($svg)->not->toContain('<?xml');
});

test('a document is uploaded and downloadable', function () {
    Storage::fake('public');

    $this->actingAs(documentManager('site.manage'))
        ->post(route('site.documents.store'), [
            'category' => 'procurement',
            'title' => 'ประกาศประกวดราคา',
            'is_published' => '1',
            'document_file' => UploadedFile::fake()->create('ประกาศ.pdf', 300, 'application/pdf'),
        ])
        ->assertRedirect();

    $document = SiteDocument::firstOrFail();

    expect($document->file_original_name)->toBe('ประกาศ.pdf')
        ->and($document->file_extension)->toBe('pdf');

    Storage::disk('public')->assertExists($document->file_path);

    $response = $this->get(route('site.document.download', $document))->assertOk();

    $disposition = $response->headers->get('content-disposition');

    expect($disposition)->toContain(rawurlencode('ประกาศ.pdf'))
        ->and($disposition)->toMatch('/filename="?document-\d+\.pdf"?/');
});

test('downloading counts the download', function () {
    Storage::fake('public');

    $document = SiteDocument::factory()->create();
    Storage::disk('public')->put($document->file_path, 'contents');

    $this->get(route('site.document.download', $document))->assertOk();

    expect($document->refresh()->download_count)->toBe(1);
});

test('a missing file is a 404 rather than a broken download', function () {
    Storage::fake('public');

    $document = SiteDocument::factory()->create();

    $this->get(route('site.document.download', $document))->assertNotFound();
});

test('publishing without a time still appears', function () {
    Storage::fake('public');

    $this->actingAs(documentManager('site.manage'))
        ->post(route('site.documents.store'), [
            'category' => 'job',
            'title' => 'รับสมัครนักวิชาการ',
            'is_published' => '1',
            'document_file' => UploadedFile::fake()->create('job.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    expect(SiteDocument::firstOrFail()->published_at)->not->toBeNull();

    $this->get(route('site.documents'))->assertOk()->assertSee('รับสมัครนักวิชาการ');
});

test('an executable is rejected', function () {
    Storage::fake('public');

    $this->actingAs(documentManager('site.manage'))
        ->post(route('site.documents.store'), [
            'category' => 'other',
            'title' => 'ทดสอบ',
            'document_file' => UploadedFile::fake()->create('payload.exe', 10, 'application/octet-stream'),
        ])
        ->assertSessionHasErrors('document_file');

    expect(SiteDocument::count())->toBe(0);
});

test('an unknown category is rejected', function () {
    Storage::fake('public');

    $this->actingAs(documentManager('site.manage'))
        ->post(route('site.documents.store'), [
            'category' => 'made-up',
            'title' => 'ทดสอบ',
            'document_file' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf'),
        ])
        ->assertSessionHasErrors('category');
});

test('replacing the file removes the one it replaced', function () {
    Storage::fake('public');

    $manager = documentManager('site.manage');

    $this->actingAs($manager)->post(route('site.documents.store'), [
        'category' => 'other',
        'title' => 'เอกสาร',
        'document_file' => UploadedFile::fake()->create('first.pdf', 100, 'application/pdf'),
    ]);

    $document = SiteDocument::firstOrFail();
    $original = $document->file_path;

    $this->actingAs($manager)->put(route('site.documents.update', $document), [
        'category' => 'other',
        'title' => 'เอกสาร',
        'document_file' => UploadedFile::fake()->create('second.pdf', 100, 'application/pdf'),
    ]);

    Storage::disk('public')->assertMissing($original);
    Storage::disk('public')->assertExists($document->refresh()->file_path);
});

test('deleting keeps the file so a restore is not left pointing at nothing', function () {
    Storage::fake('public');

    $document = SiteDocument::factory()->create();
    Storage::disk('public')->put($document->file_path, 'contents');

    $this->actingAs(documentManager('site.manage'))
        ->delete(route('site.documents.destroy', $document))
        ->assertRedirect();

    expect(SiteDocument::withTrashed()->whereKey($document->id)->exists())->toBeTrue();
    Storage::disk('public')->assertExists($document->file_path);
});

test('uploading needs the manage permission', function () {
    $this->actingAs(documentManager('site.view'))
        ->get(route('site.documents.create'))
        ->assertForbidden();
});

test('category labels come from config, not from the views', function () {
    // Changing a label should be one edit, not a sweep through templates.
    config(['site.document_categories' => ['procurement' => 'จัดซื้อจัดจ้าง (แก้ไขแล้ว)']]);

    SiteDocument::factory()->category('procurement')->create(['title' => 'เอกสาร']);

    $this->get(route('site.documents'))->assertOk()->assertSee('จัดซื้อจัดจ้าง (แก้ไขแล้ว)');
});

test('a PDF can be read in the browser', function () {
    Storage::fake('public');

    $document = SiteDocument::factory()->create(['file_extension' => 'pdf']);
    Storage::disk('public')->put($document->file_path, '%PDF-1.4 fake');

    $response = $this->get(route('site.document.preview', $document))->assertOk();

    // inline, not attachment: the browser renders it instead of saving it.
    expect($response->headers->get('content-disposition'))->toStartWith('inline')
        ->and($response->headers->get('content-type'))->toContain('application/pdf');
});

test('the preview is sandboxed and not sniffable', function () {
    Storage::fake('public');

    $document = SiteDocument::factory()->create(['file_extension' => 'pdf']);
    Storage::disk('public')->put($document->file_path, '%PDF-1.4 fake');

    $response = $this->get(route('site.document.preview', $document))->assertOk();

    // A PDF can carry scripts, and this one is served from the site's own
    // origin, so it runs with no privileges and is not re-typed by sniffing.
    expect($response->headers->get('content-security-policy'))->toContain('sandbox')
        ->and($response->headers->get('x-content-type-options'))->toBe('nosniff');
});

test('only a PDF is served inline', function (string $extension) {
    Storage::fake('public');

    $document = SiteDocument::factory()->create(['file_extension' => $extension]);
    Storage::disk('public')->put($document->file_path, 'contents');

    // The browser cannot render these, and serving arbitrary types inline from
    // our own origin buys nothing in exchange for the risk.
    $this->get(route('site.document.preview', $document))->assertNotFound();
})->with(['docx', 'xlsx', 'pptx', 'doc']);

test('a draft is not previewable either', function () {
    Storage::fake('public');

    $document = SiteDocument::factory()->draft()->create(['file_extension' => 'pdf']);
    Storage::disk('public')->put($document->file_path, '%PDF-1.4 fake');

    $this->get(route('site.document.preview', $document))->assertNotFound();
});

test('reading and downloading are counted separately', function () {
    Storage::fake('public');

    $document = SiteDocument::factory()->create(['file_extension' => 'pdf']);
    Storage::disk('public')->put($document->file_path, '%PDF-1.4 fake');

    $this->get(route('site.document.preview', $document))->assertOk();
    $this->get(route('site.document.preview', $document))->assertOk();
    $this->get(route('site.document.download', $document))->assertOk();

    $document->refresh();

    // Folding them together would inflate downloads with people who only
    // glanced at a notice.
    expect($document->view_count)->toBe(2)
        ->and($document->download_count)->toBe(1);
});

test('the page offers a view button for a PDF and not for a Word file', function () {
    $pdf = SiteDocument::factory()->create(['file_extension' => 'pdf', 'title' => 'ประกาศ PDF']);
    $word = SiteDocument::factory()->create(['file_extension' => 'docx', 'title' => 'ประกาศ Word']);

    $this->get(route('site.document', $pdf))
        ->assertOk()
        ->assertSee('แสดงไฟล์')
        ->assertSee(e(route('site.document.preview', $pdf)), false);

    $this->get(route('site.document', $word))
        ->assertOk()
        ->assertDontSee('แสดงไฟล์');
});
