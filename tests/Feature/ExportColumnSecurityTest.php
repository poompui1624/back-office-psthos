<?php

use App\Exports\DatabaseTableExport;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Permission;

test('the users export never carries credential columns', function () {
    User::factory()->create();

    $headings = (new DatabaseTableExport('users', 'Users', [
        'id', 'employee_id', 'name', 'email', 'email_verified_at',
        'is_active', 'last_login_at', 'created_at', 'updated_at',
    ]))->headings();

    expect($headings)
        ->not->toContain('password')
        ->not->toContain('remember_token')
        ->not->toContain('two_factor_secret')
        ->not->toContain('two_factor_recovery_codes')
        ->toContain('email');
});

test('an explicit allowlist cannot re-open a denied column', function () {
    $headings = (new DatabaseTableExport('users', 'Users', ['id', 'name', 'password']))->headings();

    expect($headings)->toBe(['id', 'name']);
});

test('a table exported without an allowlist still drops denied columns', function () {
    $headings = (new DatabaseTableExport('users', 'Users'))->headings();

    expect($headings)
        ->not->toContain('password')
        ->not->toContain('remember_token')
        ->toContain('name');
});

test('the employees export never carries the citizen id', function () {
    $headings = (new DatabaseTableExport('employees', 'Employees'))->headings();

    expect($headings)
        ->not->toContain('citizen_id')
        ->toContain('employee_code');
});

test('exported rows line up with the exported headings', function () {
    $user = User::factory()->create(['name' => 'ทดสอบ ระบบ']);

    $export = new DatabaseTableExport('users', 'Users', ['id', 'name', 'password', 'email']);

    $headings = $export->headings();
    $row = $export->collection()->first();

    expect($headings)->toBe(['id', 'name', 'email'])
        ->and($row)->toBe([$user->id, 'ทดสอบ ระบบ', $user->email]);
});

test('the downloaded users export contains no password hash', function () {
    $this->seed(RolePermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $response = $this->actingAs($admin)->get(route('exports.table', 'users'));

    $response->assertOk();
    expect($response->streamedContent())->not->toContain($admin->password);
});

test('a department scoped table cannot be exported without hospital wide access', function () {
    $supervisor = User::factory()->create();
    Permission::findOrCreate('leave.view');
    $supervisor->givePermissionTo('leave.view');

    // DatabaseTableExport dumps the whole table, so a supervisor exporting it
    // would walk out with every department's leave.
    $this->actingAs($supervisor)
        ->get(route('exports.table', 'leave_requests'))
        ->assertForbidden();
});

test('hospital wide access still allows the export', function () {
    $hr = User::factory()->create();

    foreach (['leave.view', 'leave.view.all'] as $permission) {
        Permission::findOrCreate($permission);
        $hr->givePermissionTo($permission);
    }

    $this->actingAs($hr)
        ->get(route('exports.table', 'leave_requests'))
        ->assertOk();
});

test('an unscoped reference table is unaffected', function () {
    $user = User::factory()->create();
    Permission::findOrCreate('department.view');
    $user->givePermissionTo('department.view');

    $this->actingAs($user)
        ->get(route('exports.table', 'departments'))
        ->assertOk();
});
