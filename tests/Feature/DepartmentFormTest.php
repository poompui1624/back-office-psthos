<?php

use App\Models\Department;
use App\Models\User;
use Spatie\Permission\Models\Permission;

/**
 * Guards the round trip through the converted form.
 *
 * PageSmokeTest proves the page renders; it cannot tell whether an input
 * still posts under the name the controller validates on. Rewriting raw
 * <input> tags into <x-form.input> is exactly where that could break.
 */
function departmentUser(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    return $user;
}

test('the create form posts every field under the name the controller expects', function () {
    $parent = Department::factory()->create();

    $this->actingAs(departmentUser('department.create'))
        ->post(route('departments.store'), [
            'parent_id' => $parent->id,
            'code' => 'ICU',
            'name' => 'หอผู้ป่วยวิกฤต',
            'type' => 'unit',
            'is_active' => 1,
        ])
        ->assertRedirect();

    $created = Department::where('code', 'ICU')->firstOrFail();

    expect($created->name)->toBe('หอผู้ป่วยวิกฤต')
        ->and($created->parent_id)->toBe($parent->id)
        ->and($created->type)->toBe('unit')
        ->and($created->is_active)->toBeTrue();
});

test('the edit form updates every field', function () {
    $department = Department::factory()->create(['code' => 'OLD', 'name' => 'เดิม']);

    $this->actingAs(departmentUser('department.update'))
        ->put(route('departments.update', $department), [
            'code' => 'NEW',
            'name' => 'ใหม่',
            'type' => 'group',
        ])
        ->assertRedirect();

    $department->refresh();

    expect($department->code)->toBe('NEW')
        ->and($department->name)->toBe('ใหม่')
        ->and($department->type)->toBe('group');
});

test('validation errors come back to the field', function () {
    $this->actingAs(departmentUser('department.create'))
        ->post(route('departments.store'), ['code' => '', 'name' => ''])
        ->assertSessionHasErrors(['code', 'name']);

    expect(Department::count())->toBe(0);
});

test('the form redisplays the submitted values after a validation failure', function () {
    $user = departmentUser('department.create');

    $this->actingAs($user)
        ->post(route('departments.store'), ['code' => 'DUP', 'name' => '']);

    // old() has to survive the component rewrite, or a failed submit wipes the form.
    $this->actingAs($user)
        ->get(route('departments.create'))
        ->assertOk()
        ->assertSee('value="DUP"', false);
});

test('the edit form shows the current values', function () {
    $department = Department::factory()->create(['code' => 'ER', 'name' => 'ห้องฉุกเฉิน']);

    $this->actingAs(departmentUser('department.update'))
        ->get(route('departments.edit', $department))
        ->assertOk()
        ->assertSee('value="ER"', false)
        ->assertSee('ห้องฉุกเฉิน');
});
