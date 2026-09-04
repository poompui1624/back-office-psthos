<?php

use App\Models\SystemSetting;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function systemSettingsUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

function makeSystemSetting(array $attributes): SystemSetting
{
    return SystemSetting::create(array_merge([
        'group' => 'general',
        'key' => 'hospital.name',
        'label' => 'ชื่อโรงพยาบาล',
        'type' => 'text',
        'value' => 'เดิม',
        'is_active' => true,
    ], $attributes));
}

test('the settings page renders inside the same shell as every other page', function () {
    makeSystemSetting([]);

    $response = $this->actingAs(systemSettingsUser('setting.view'))->get(route('system-settings.index'));

    // This page was the only one on <x-layouts::app>, the starter kit's
    // separate layout, so it carried a different sidebar entirely.
    $response->assertOk()
        ->assertSee('ตั้งค่าระบบ')
        ->assertSee('id="app-sidebar-toggle"', false)
        ->assertSee('Back-office System');
});

test('every input type still posts under settings[key]', function () {
    makeSystemSetting(['key' => 'hospital.name', 'type' => 'text', 'value' => 'เดิม']);
    makeSystemSetting(['key' => 'leave.max_days', 'type' => 'number', 'value' => '10', 'label' => 'วันลาสูงสุด', 'group' => 'leave']);
    makeSystemSetting(['key' => 'hospital.address', 'type' => 'textarea', 'value' => 'ที่อยู่เดิม', 'label' => 'ที่อยู่']);
    makeSystemSetting(['key' => 'meeting.approval', 'type' => 'boolean', 'value' => '0', 'label' => 'ต้องอนุมัติ', 'group' => 'meeting']);
    makeSystemSetting(['key' => 'attendance.start', 'type' => 'time', 'value' => '08:00', 'label' => 'เวลาเข้างาน', 'group' => 'attendance']);

    $this->actingAs(systemSettingsUser('setting.view', 'setting.update'))
        ->put(route('system-settings.update'), [
            'settings' => [
                'hospital.name' => 'โรงพยาบาลใหม่',
                'leave.max_days' => '15',
                'hospital.address' => 'ที่อยู่ใหม่',
                'meeting.approval' => '1',
                'attendance.start' => '07:30',
            ],
        ])
        ->assertRedirect();

    expect(SystemSetting::where('key', 'hospital.name')->value('value'))->toBe('โรงพยาบาลใหม่')
        ->and(SystemSetting::where('key', 'leave.max_days')->value('value'))->toBe('15')
        ->and(SystemSetting::where('key', 'hospital.address')->value('value'))->toBe('ที่อยู่ใหม่')
        ->and(SystemSetting::where('key', 'meeting.approval')->value('value'))->toBe('1')
        ->and(SystemSetting::where('key', 'attendance.start')->value('value'))->toBe('07:30');
});

test('the current value is shown in the field', function () {
    makeSystemSetting(['key' => 'hospital.name', 'value' => 'โรงพยาบาลปางศิลาทอง']);

    $this->actingAs(systemSettingsUser('setting.view'))
        ->get(route('system-settings.index'))
        ->assertOk()
        ->assertSee('value="โรงพยาบาลปางศิลาทอง"', false);
});

test('a boolean setting offers both options with the stored one selected', function () {
    makeSystemSetting(['key' => 'meeting.approval', 'type' => 'boolean', 'value' => '1', 'label' => 'ต้องอนุมัติ']);

    $this->actingAs(systemSettingsUser('setting.view'))
        ->get(route('system-settings.index'))
        ->assertOk()
        ->assertSee('เปิดใช้งาน')
        ->assertSee('ปิดใช้งาน');
});

test('saving needs the update permission', function () {
    makeSystemSetting([]);

    $this->actingAs(systemSettingsUser('setting.view'))
        ->put(route('system-settings.update'), ['settings' => ['hospital.name' => 'แอบแก้']])
        ->assertForbidden();

    expect(SystemSetting::where('key', 'hospital.name')->value('value'))->toBe('เดิม');
});
