<?php

use App\Models\ShiftType;
use App\Models\User;
use Spatie\Permission\Models\Permission;

test('the shift list renders a colour swatch for a coloured shift', function () {
    ShiftType::factory()->create(['color' => 'blue', 'code' => 'SW1']);

    $user = User::factory()->create();

    foreach (['duty.view', 'duty.view.all'] as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    // The swatch class is looked up from a map rather than interpolated:
    // Tailwind scans source statically, so bg-{$colour}-500 was never built.
    $this->actingAs($user)
        ->get(route('shift-types.index'))
        ->assertOk()
        ->assertSee('SW1')
        ->assertSee('bg-blue-500', false);
});

test('a shift with no colour renders without a swatch', function () {
    ShiftType::factory()->create(['color' => null, 'code' => 'SW2']);

    $user = User::factory()->create();

    foreach (['duty.view', 'duty.view.all'] as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }

    $this->actingAs($user)
        ->get(route('shift-types.index'))
        ->assertOk()
        ->assertSee('SW2')
        ->assertDontSee('rounded-full bg-blue-500', false);
});
