<?php

use App\Models\Asset;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function policyUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('policies are discovered for their models', function (string $model, string $prefix) {
    $record = $model::factory()->create();
    $user = policyUser("{$prefix}.view", "{$prefix}.view.all");

    expect($user->can('view', $record))->toBeTrue()
        ->and($user->can('viewAny', $model))->toBeTrue();
})->with([
    [LeaveRequest::class, 'leave'],
    [Employee::class, 'employee'],
    [Asset::class, 'asset'],
    [PayrollPeriod::class, 'payroll'],
]);

test('each verb maps to its own permission', function () {
    $record = LeaveRequest::factory()->create();
    $viewer = policyUser('leave.view', 'leave.view.all');

    expect($viewer->can('view', $record))->toBeTrue()
        ->and($viewer->can('create', LeaveRequest::class))->toBeFalse()
        ->and($viewer->can('update', $record))->toBeFalse()
        ->and($viewer->can('delete', $record))->toBeFalse();
});

test('a user without the module permission is denied outright', function () {
    $record = LeaveRequest::factory()->create();
    $stranger = policyUser('asset.view');

    expect($stranger->can('view', $record))->toBeFalse()
        ->and($stranger->can('viewAny', LeaveRequest::class))->toBeFalse();
});

test('approving is gated on the approve permission, not update', function () {
    $leave = LeaveRequest::factory()->create();

    expect(policyUser('leave.approve', 'leave.view.all')->can('approve', $leave))->toBeTrue()
        ->and(policyUser('leave.update', 'leave.view.all')->can('approve', $leave))->toBeFalse();
});

test('modules sharing a prefix share their permissions', function () {
    $asset = Asset::factory()->create();
    $user = policyUser('asset.view', 'asset.update', 'asset.view.all');

    expect($user->can('view', $asset))->toBeTrue()
        ->and($user->can('update', $asset))->toBeTrue();
});
