<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    Permission::findOrCreate('dashboard.view');
    $user->givePermissionTo('dashboard.view');

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});
