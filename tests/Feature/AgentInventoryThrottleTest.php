<?php

use App\Models\ComputerAgent;
use App\Models\ComputerSnapshot;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    // The limiter counts in the cache, which persists across tests otherwise.
    RateLimiter::clear('agent-anon:127.0.0.1');
    cache()->clear();
});

function agentToken(): string
{
    $plain = 'hospital-agent-'.str_repeat('a', 40);

    ComputerAgent::create([
        'name' => 'ward-rollout',
        'token_hash' => ComputerAgent::hashToken($plain),
        'is_active' => true,
    ]);

    return $plain;
}

/**
 * @param  array<string, mixed>  $overrides
 */
function postInventory(?string $token, array $overrides = []): TestResponse
{
    return test()->postJson(
        route('api.agent.computer-inventory'),
        array_merge(['hostname' => 'PC-001', 'machine_uuid' => 'uuid-001'], $overrides),
        $token ? ['Authorization' => 'Bearer '.$token] : []
    );
}

test('a request with no token is rejected before it can be repeated freely', function () {
    // Ten get through the limiter and are refused by the controller as
    // unauthorized; the eleventh does not reach the controller at all.
    foreach (range(1, 10) as $ignored) {
        postInventory(null)->assertStatus(401);
    }

    postInventory(null)->assertStatus(429);
});

test('one machine cannot flood the endpoint', function () {
    $token = agentToken();

    foreach (range(1, 6) as $ignored) {
        postInventory($token)->assertOk();
    }

    postInventory($token)->assertStatus(429);

    // Six accepted reports, six snapshot rows — the seventh wrote nothing.
    expect(ComputerSnapshot::count())->toBe(6);
});

test('a fleet sharing one token is not throttled by its neighbours', function () {
    $token = agentToken();

    // Twenty machines reporting at once, which is what a morning rollout looks
    // like. Keying the per-minute limit on the token would reject most of these.
    foreach (range(1, 20) as $i) {
        postInventory($token, [
            'hostname' => 'PC-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            'machine_uuid' => 'uuid-'.$i,
        ])->assertOk();
    }

    expect(ComputerSnapshot::count())->toBe(20);
});

test('a machine without a uuid still gets its own budget by mac then hostname', function () {
    $token = agentToken();

    foreach (range(1, 6) as $ignored) {
        postInventory($token, ['machine_uuid' => null, 'mac_address' => 'AA:BB:CC:DD:EE:01'])->assertOk();
    }

    postInventory($token, ['machine_uuid' => null, 'mac_address' => 'AA:BB:CC:DD:EE:01'])->assertStatus(429);

    // A different machine on the same token is unaffected.
    postInventory($token, ['machine_uuid' => null, 'mac_address' => 'AA:BB:CC:DD:EE:02', 'hostname' => 'PC-002'])
        ->assertOk();
});

test('a throttled agent gets JSON rather than an HTML error page', function () {
    $token = agentToken();

    foreach (range(1, 6) as $ignored) {
        postInventory($token);
    }

    $response = postInventory($token)->assertStatus(429);

    expect($response->headers->get('content-type'))->toContain('application/json')
        ->and($response->headers->has('Retry-After'))->toBeTrue();
});

test('an inactive agent is refused even with a valid token', function () {
    $token = agentToken();
    ComputerAgent::query()->update(['is_active' => false]);

    postInventory($token)->assertStatus(401);

    expect(ComputerSnapshot::count())->toBe(0);
});

test('rotating the machine identity does not buy unlimited writes', function () {
    $token = agentToken();

    // The per-minute limit is keyed on a machine identity read from the body,
    // so a stolen token could invent a new one every request. The hourly
    // ceiling on the token is what stops that.
    $accepted = 0;

    foreach (range(1, 2000) as $i) {
        $status = postInventory($token, ['hostname' => 'PC-'.$i, 'machine_uuid' => 'uuid-'.$i])->status();

        if ($status === 429) {
            break;
        }

        $accepted++;
    }

    expect($accepted)->toBe(2000)
        ->and(postInventory($token, ['machine_uuid' => 'uuid-fresh'])->status())->toBe(429);
})->group('slow');
