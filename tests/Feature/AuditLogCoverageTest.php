<?php

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\SalaryProfile;

test('business writes are recorded in the audit log', function (string $model) {
    $record = $model::factory()->create();

    $logged = AuditLog::where('auditable_type', $model)
        ->where('auditable_id', $record->getKey())
        ->where('action', 'created')
        ->exists();

    expect($logged)->toBeTrue();
})->with([
    Employee::class,
    LeaveRequest::class,
    SalaryProfile::class,
    PayrollPeriod::class,
]);

test('updates record what actually changed', function () {
    $period = PayrollPeriod::factory()->create(['status' => 'draft']);

    AuditLog::query()->delete();

    $period->update(['status' => 'generated']);

    $log = AuditLog::where('action', 'updated')->firstOrFail();

    expect($log->new_values)->toHaveKey('status')
        ->and($log->new_values['status'])->toBe('generated')
        ->and($log->old_values['status'])->toBe('draft')
        ->and($log->new_values)->not->toHaveKey('updated_at');
});

test('the citizen id never reaches the audit log', function () {
    $employee = Employee::factory()->create(['citizen_id' => '1234567890123']);

    $logs = AuditLog::where('auditable_type', Employee::class)->get();

    expect($logs)->not->toBeEmpty();

    foreach ($logs as $log) {
        expect($log->new_values ?? [])->not->toHaveKey('citizen_id')
            ->and($log->old_values ?? [])->not->toHaveKey('citizen_id');
    }

    // The value is still stored on the record itself, just not in the log.
    expect($employee->refresh()->citizen_id)->toBe('1234567890123');
});
