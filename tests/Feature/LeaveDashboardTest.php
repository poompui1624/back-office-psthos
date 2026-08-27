<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

test('authenticated users with leave permission can visit leave dashboard', function () {
    $user = User::factory()->create();

    Permission::findOrCreate('leave.view');
    $user->givePermissionTo('leave.view');

    $response = $this
        ->actingAs($user)
        ->get(route('leave-requests.dashboard'));

    $response
        ->assertOk()
        ->assertSee('รายละเอียดโมดูลการลา')
        ->assertSee('ยังไม่มีรายการลาในเดือนนี้');
});
