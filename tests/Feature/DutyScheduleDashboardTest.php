<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

test('authenticated users with duty permission can visit duty schedule dashboard', function () {
    $user = User::factory()->create();

    Permission::findOrCreate('duty.view');
    $user->givePermissionTo('duty.view');

    $response = $this
        ->actingAs($user)
        ->get(route('duty-schedules.index'));

    $response
        ->assertOk()
        ->assertSee('ระบบจัดตารางเวรทุกหน่วยงาน')
        ->assertSee('ยังไม่มีตารางเวรตามเงื่อนไขนี้');
});
