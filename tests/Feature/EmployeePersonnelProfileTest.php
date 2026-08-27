<?php

use App\Models\Employee;
use App\Models\User;
use Spatie\Permission\Models\Permission;

test('authorized users can open employee personnel profile page', function () {
    $user = User::factory()->create();
    $employee = Employee::create([
        'employee_code' => 'EMP001',
        'first_name' => 'Somchai',
        'last_name' => 'Dee',
        'status' => 'active',
    ]);

    Permission::findOrCreate('employee.sensitive.view');
    $user->givePermissionTo('employee.sensitive.view');

    $response = $this
        ->actingAs($user)
        ->get(route('employees.personnel-profile.edit', $employee));

    $response
        ->assertOk()
        ->assertSee('ข้อมูล ก.พ.7')
        ->assertSee('ประวัติการศึกษา');
});

test('users with employee update permission can open personnel profile as a legacy fallback', function () {
    $user = User::factory()->create();
    $employee = Employee::create([
        'employee_code' => 'EMP003',
        'first_name' => 'Legacy',
        'last_name' => 'User',
        'status' => 'active',
    ]);

    Permission::findOrCreate('employee.update');
    $user->givePermissionTo('employee.update');

    $response = $this
        ->actingAs($user)
        ->get(route('employees.personnel-profile.edit', $employee));

    $response
        ->assertOk()
        ->assertSee('ข้อมูล ก.พ.7');
});

test('authorized users can update employee personnel profile', function () {
    $user = User::factory()->create();
    $employee = Employee::create([
        'employee_code' => 'EMP002',
        'first_name' => 'Suda',
        'last_name' => 'Dee',
        'status' => 'active',
    ]);

    Permission::findOrCreate('employee.sensitive.update');
    $user->givePermissionTo('employee.sensitive.update');

    $response = $this
        ->actingAs($user)
        ->put(route('employees.personnel-profile.update', $employee), [
            'nationality' => 'ไทย',
            'blood_type' => 'O',
            'registered_address' => [
                'house_no' => '99',
                'province' => 'น่าน',
            ],
            'education_histories' => [
                [
                    'level' => 'ปริญญาตรี',
                    'institution' => 'มหาวิทยาลัยทดสอบ',
                    'degree' => 'พยาบาลศาสตรบัณฑิต',
                    'graduated_year' => '2560',
                ],
                [
                    'level' => '',
                    'institution' => '',
                    'degree' => '',
                    'graduated_year' => '',
                ],
            ],
        ]);

    $response->assertRedirect(route('employees.personnel-profile.edit', $employee));

    $employee->refresh();

    expect($employee->personnelProfile)->not->toBeNull()
        ->and($employee->personnelProfile->nationality)->toBe('ไทย')
        ->and($employee->personnelProfile->registered_address['province'])->toBe('น่าน')
        ->and($employee->personnelProfile->education_histories)->toHaveCount(1);
});
