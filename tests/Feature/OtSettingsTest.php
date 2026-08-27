<?php

use App\Models\ShiftType;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function settingsUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

function shiftPayload(array $overrides = []): array
{
    return array_merge([
        'code' => 'OT1',
        'name' => 'เวรล่วงเวลา',
        'start_time' => '18:00',
        'end_time' => '22:00',
        'is_active' => 1,
    ], $overrides);
}

test('a shift can be marked as overtime with a multiplier', function () {
    $this->actingAs(settingsUser('duty.create'))
        ->post(route('shift-types.store'), shiftPayload(['is_ot' => 1, 'ot_multiplier' => 1.5]));

    $shift = ShiftType::firstOrFail();

    expect($shift->is_ot)->toBeTrue()
        ->and((float) $shift->ot_multiplier)->toBe(1.5)
        ->and($shift->ot_flat_rate)->toBeNull();
});

test('a blank multiplier defaults to one rather than zero', function () {
    // Saving 0 here would silently make every OT shift unpaid.
    $this->actingAs(settingsUser('duty.create'))
        ->post(route('shift-types.store'), shiftPayload(['is_ot' => 1]));

    expect((float) ShiftType::firstOrFail()->ot_multiplier)->toBe(1.0);
});

test('an empty flat rate stays null so the multiplier is used', function () {
    $this->actingAs(settingsUser('duty.create'))
        ->post(route('shift-types.store'), shiftPayload(['is_ot' => 1, 'ot_flat_rate' => '']));

    expect(ShiftType::firstOrFail()->ot_flat_rate)->toBeNull();
});

test('a flat rate is stored when given', function () {
    $this->actingAs(settingsUser('duty.create'))
        ->post(route('shift-types.store'), shiftPayload(['is_ot' => 1, 'ot_flat_rate' => 750]));

    expect((float) ShiftType::firstOrFail()->ot_flat_rate)->toBe(750.0);
});

test('a shift left unticked is not overtime', function () {
    $this->actingAs(settingsUser('duty.create'))
        ->post(route('shift-types.store'), shiftPayload());

    expect(ShiftType::firstOrFail()->is_ot)->toBeFalse();
});

test('an absurd multiplier is rejected', function () {
    $this->actingAs(settingsUser('duty.create'))
        ->post(route('shift-types.store'), shiftPayload(['is_ot' => 1, 'ot_multiplier' => 99]))
        ->assertSessionHasErrors('ot_multiplier');

    expect(ShiftType::count())->toBe(0);
});

test('the shift form offers the overtime fields', function () {
    $this->actingAs(settingsUser('duty.create'))
        ->get(route('shift-types.create'))
        ->assertOk()
        ->assertSee('นับเป็นเวรล่วงเวลา (OT)')
        ->assertSee('ตัวคูณ OT')
        ->assertSee('ค่า OT เหมาจ่าย (บาท)');
});

test('the salary form offers the hourly overtime rate', function () {
    $this->actingAs(settingsUser('payroll.create'))
        ->get(route('salary-profiles.create'))
        ->assertOk()
        ->assertSee('อัตรา OT / ชั่วโมง');
});
