<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

/**
 * Renders each UI component in isolation.
 *
 * The sweep replaces hand-written markup across ~150 pages with these, so a
 * regression here would land everywhere at once.
 */
function render(string $template, array $data = []): string
{
    // A real request always has $errors shared by ShareErrorsFromSession.
    // Blade::render() runs outside that stack, so supply an empty bag here
    // rather than making every component defend against its absence.
    $data['errors'] ??= new ViewErrorBag;

    return trim(Blade::render($template, $data));
}

/**
 * Render with a populated error bag.
 *
 * Components resolve $errors from shared view data, not from the caller's
 * array, so the bag has to be shared the way the middleware shares it.
 */
function renderWithError(string $template, string $field, string $message): string
{
    $bag = new ViewErrorBag;
    $bag->put('default', new MessageBag([$field => $message]));

    View::share('errors', $bag);

    try {
        return trim(Blade::render($template));
    } finally {
        View::share('errors', new ViewErrorBag);
    }
}

test('a button renders as a button by default and a link when given an href', function () {
    expect(render('<x-btn>บันทึก</x-btn>'))
        ->toContain('<button', 'type="submit"', 'บันทึก')
        ->and(render('<x-btn href="/x">ไป</x-btn>'))
        ->toContain('<a', 'href="/x"');
});

test('every button variant produces its own colour', function (string $variant, string $expected) {
    expect(render('<x-btn variant="'.$variant.'">x</x-btn>'))->toContain($expected);
})->with([
    ['primary', 'bg-brand-500'],
    ['secondary', 'bg-white'],
    ['danger', 'bg-rose-600'],
    ['warning', 'bg-amber-500'],
    ['ghost', 'hover:bg-slate-100'],
]);

test('an unknown button variant falls back to primary rather than rendering unstyled', function () {
    expect(render('<x-btn variant="nonsense">x</x-btn>'))->toContain('bg-brand-500');
});

test('a button can carry an icon', function () {
    expect(render('<x-btn icon="search">ค้นหา</x-btn>'))->toContain('<svg', 'ค้นหา');
});

test('badges render their tone and optional dot', function () {
    expect(render('<x-badge tone="success">ใช้งาน</x-badge>'))
        ->toContain('bg-emerald-100', 'ใช้งาน')
        ->and(render('<x-badge tone="danger" :dot="true">หยุด</x-badge>'))
        ->toContain('bg-rose-100', 'rounded-full');
});

test('the page header shows a title, an optional subtitle, and actions', function () {
    $html = render('<x-page-header title="ทะเบียน" subtitle="คำอธิบาย"><x-btn>เพิ่ม</x-btn></x-page-header>');

    expect($html)->toContain('ทะเบียน', 'คำอธิบาย', 'เพิ่ม');

    expect(render('<x-page-header title="ไม่มีคำอธิบาย" />'))
        ->toContain('ไม่มีคำอธิบาย')
        ->not->toContain('mt-1 text-sm text-slate-500');
});

test('alerts render each type', function (string $type, string $expected) {
    expect(render('<x-alert type="'.$type.'">ข้อความ</x-alert>'))->toContain($expected, 'ข้อความ');
})->with([
    ['success', 'bg-emerald-50'],
    ['error', 'bg-rose-50'],
    ['warning', 'bg-amber-50'],
    ['info', 'bg-sky-50'],
]);

test('the empty state shows its message and any action', function () {
    expect(render('<x-empty-state title="ไม่พบข้อมูล" description="ลองเปลี่ยนคำค้น"><x-btn>เพิ่ม</x-btn></x-empty-state>'))
        ->toContain('ไม่พบข้อมูล', 'ลองเปลี่ยนคำค้น', 'เพิ่ม');
});

test('the table shell renders headers and rows', function () {
    $html = render(<<<'BLADE'
        <x-data-table>
            <x-slot:head>
                <x-data-table.th>รหัส</x-data-table.th>
                <x-data-table.th align="center">สถานะ</x-data-table.th>
            </x-slot:head>

            <x-data-table.row>
                <x-data-table.td>D01</x-data-table.td>
                <x-data-table.td align="center">ใช้งาน</x-data-table.td>
            </x-data-table.row>
        </x-data-table>
    BLADE);

    expect($html)
        ->toContain('<thead', 'รหัส', 'text-center', 'D01', 'ใช้งาน')
        ->and($html)->toContain('overflow-x-auto');
});

test('the table empty row spans every column', function () {
    expect(render('<x-data-table.empty :colspan="6" title="ว่าง" />'))
        ->toContain('colspan="6"', 'ว่าง');
});

test('a field renders its label and marks required ones', function () {
    expect(render('<x-form.field label="ชื่อ" name="name" />'))
        ->toContain('ชื่อ', 'for="name"')
        ->not->toContain('text-rose-600');

    expect(render('<x-form.field label="ชื่อ" name="name" :required="true" />'))
        ->toContain('text-rose-600');
});

test('a field shows the validation message instead of the hint', function () {
    $html = renderWithError('<x-form.field label="รหัส" name="code" hint="ตัวอย่าง IT" />', 'code', 'ต้องกรอกรหัส');

    expect($html)->toContain('ต้องกรอกรหัส')->not->toContain('ตัวอย่าง IT');
});

test('a field shows the hint when there is no error', function () {
    expect(render('<x-form.field label="รหัส" name="code" hint="ตัวอย่าง IT" />'))
        ->toContain('ตัวอย่าง IT');
});

test('inputs keep the name the controller validates on', function () {
    // The sweep rewrites raw <input> tags into this component; a dropped or
    // renamed attribute here would silently stop a form from saving.
    $html = render('<x-form.input name="employee_code" value="EMP001" placeholder="รหัส" />');

    expect($html)->toContain('name="employee_code"', 'id="employee_code"', 'value="EMP001"', 'placeholder="รหัส"');
});

test('inputs accept a type and extra attributes', function () {
    expect(render('<x-form.input name="price" type="number" step="0.01" min="0" />'))
        ->toContain('type="number"', 'step="0.01"', 'min="0"');
});

test('an input turns red when its field failed validation', function () {
    expect(renderWithError('<x-form.input name="code" />', 'code', 'ผิด'))->toContain('border-rose-300')
        ->and(render('<x-form.input name="code" />'))->toContain('border-slate-200');
});

test('a textarea puts its value in the body, not an attribute', function () {
    $html = render('<x-form.textarea name="remark" value="หมายเหตุ" rows="5" />');

    expect($html)->toContain('name="remark"', 'rows="5"', '>หมายเหตุ</textarea>');
});

test('a select keeps its options and name', function () {
    $html = render('<x-form.select name="status"><option value="active">ใช้งาน</option></x-form.select>');

    expect($html)->toContain('name="status"', 'value="active"', 'ใช้งาน');
});

test('a checkbox posts a value and reflects its checked state', function () {
    expect(render('<x-form.checkbox name="is_active" label="เปิดใช้งาน" :checked="true" />'))
        ->toContain('name="is_active"', 'value="1"', 'checked', 'เปิดใช้งาน')
        ->and(render('<x-form.checkbox name="is_active" label="x" />'))
        ->not->toContain('checked');
});

test('form actions render submit and cancel', function () {
    expect(render('<x-form.actions cancel="/back" />'))
        ->toContain('บันทึก', 'ย้อนกลับ', 'href="/back"');

    expect(render('<x-form.actions />'))->not->toContain('ย้อนกลับ');
});

test('the filter bar posts to its action and offers a clear link', function () {
    $html = render('<x-filter-bar action="/departments"><x-form.input name="search" /></x-filter-bar>');

    expect($html)->toContain('action="/departments"', 'method="GET"', 'name="search"', 'ค้นหา', 'ล้าง');

    expect(render('<x-filter-bar action="/x" :reset="false" />'))->not->toContain('ล้าง');
});
