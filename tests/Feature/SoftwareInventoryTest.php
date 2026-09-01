<?php

use App\Models\Computer;
use App\Models\ComputerAgent;
use App\Models\ComputerSnapshot;
use App\Models\ComputerSoftware;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    cache()->clear();
    RateLimiter::clear('agent-anon:127.0.0.1');
});

function inventoryUser(): User
{
    $user = User::factory()->create();
    Permission::findOrCreate('software.view');
    $user->givePermissionTo('software.view');

    return $user;
}

function inventoryAgentToken(): string
{
    $plain = 'hospital-agent-'.str_repeat('b', 40);

    ComputerAgent::create([
        'name' => 'inventory-test',
        'token_hash' => ComputerAgent::hashToken($plain),
        'is_active' => true,
    ]);

    return $plain;
}

/**
 * @param  array<int, array<string, mixed>>  $software
 */
function reportInventory(string $token, string $hostname, array $software, ?string $uuid = null): TestResponse
{
    return test()->postJson(route('api.agent.computer-inventory'), [
        'hostname' => $hostname,
        'machine_uuid' => $uuid ?? 'uuid-'.$hostname,
        'installed_software' => $software,
    ], ['Authorization' => 'Bearer '.$token]);
}

/**
 * @return array<int, array<string, string>>
 */
function chromeAndOffice(): array
{
    return [
        ['name' => 'Google Chrome', 'version' => '120.0', 'publisher' => 'Google LLC'],
        ['name' => 'Microsoft Office', 'version' => '2021', 'publisher' => 'Microsoft'],
    ];
}

// ---------------------------------------------------------------------------
// Writing
// ---------------------------------------------------------------------------

test('a report fills computer_software so the page has something to query', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();

    expect(ComputerSoftware::count())->toBe(2)
        ->and(ComputerSoftware::where('normalized_name', 'google chrome')->exists())->toBeTrue();
});

test('uninstalled software stops being reported', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();
    expect(ComputerSoftware::count())->toBe(2);

    // Office is gone from the second report.
    reportInventory($token, 'PC-001', [
        ['name' => 'Google Chrome', 'version' => '120.0', 'publisher' => 'Google LLC'],
    ])->assertOk();

    expect(ComputerSoftware::count())->toBe(1)
        ->and(ComputerSoftware::first()->normalized_name)->toBe('google chrome');
});

test('a package first seen earlier keeps that date when it is reported again', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();

    $firstSeen = ComputerSoftware::where('normalized_name', 'google chrome')->value('first_seen_at');

    $this->travel(2)->days();
    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();

    $row = ComputerSoftware::where('normalized_name', 'google chrome')->first();

    expect($row->first_seen_at->toDateTimeString())->toBe($firstSeen->toDateTimeString())
        ->and($row->last_seen_at->greaterThan($row->first_seen_at))->toBeTrue();
});

test('a machine listing the same package twice does not break the upsert', function () {
    $token = inventoryAgentToken();

    // Registry uninstall keys genuinely do carry duplicates.
    reportInventory($token, 'PC-001', [
        ['name' => 'Google Chrome', 'version' => '120.0', 'publisher' => 'Google LLC'],
        ['name' => 'Google  Chrome', 'version' => '120.0', 'publisher' => 'Google LLC'],
    ])->assertOk();

    expect(ComputerSoftware::count())->toBe(1);
});

// ---------------------------------------------------------------------------
// Snapshot growth
// ---------------------------------------------------------------------------

test('an unchanged report does not write another snapshot', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();
    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();
    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();

    // Three reports, one snapshot: the software did not change.
    expect(ComputerSnapshot::count())->toBe(1);
});

test('an unchanged report still refreshes when the machine was last seen', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();
    $firstSeen = Computer::first()->last_seen_at;

    $this->travel(1)->day();
    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();

    expect(Computer::first()->last_seen_at->greaterThan($firstSeen))->toBeTrue();
});

test('a changed report writes a new snapshot', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();

    reportInventory($token, 'PC-001', [
        ['name' => 'Google Chrome', 'version' => '121.0', 'publisher' => 'Google LLC'],
        ['name' => 'Microsoft Office', 'version' => '2021', 'publisher' => 'Microsoft'],
    ])->assertOk();

    expect(ComputerSnapshot::count())->toBe(2);
});

test('reordering the same list is not treated as a change', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();
    reportInventory($token, 'PC-001', array_reverse(chromeAndOffice()))->assertOk();

    expect(ComputerSnapshot::count())->toBe(1);
});

// ---------------------------------------------------------------------------
// Components
// ---------------------------------------------------------------------------

test('runtimes and drivers are flagged as components', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', [
        ['name' => 'Google Chrome', 'version' => '120.0', 'publisher' => 'Google LLC'],
        ['name' => 'Microsoft Visual C++ 2015-2022 Redistributable (x64)', 'version' => '14.0', 'publisher' => 'Microsoft'],
        ['name' => 'Intel(R) Chipset Device Software', 'version' => '10.1', 'publisher' => 'Intel'],
        ['name' => 'Update for Windows 10', 'version' => '1.0', 'publisher' => 'Microsoft'],
    ])->assertOk();

    expect(ComputerSoftware::where('is_component', true)->count())->toBe(3)
        ->and(ComputerSoftware::where('is_component', false)->count())->toBe(1);
});

test('components are hidden until asked for', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', [
        ['name' => 'Google Chrome', 'version' => '120.0', 'publisher' => 'Google LLC'],
        ['name' => 'Microsoft Visual C++ 2015 Redistributable', 'version' => '14.0', 'publisher' => 'Microsoft'],
    ])->assertOk();

    $this->actingAs(inventoryUser())
        ->get(route('software-inventory.index', ['search' => 'o']))
        ->assertOk()
        ->assertSee('Google Chrome')
        ->assertDontSee('Visual C++');

    $this->actingAs(inventoryUser())
        ->get(route('software-inventory.index', ['search' => 'o', 'include_components' => '1']))
        ->assertOk()
        ->assertSee('Visual C++');
});

// ---------------------------------------------------------------------------
// The page
// ---------------------------------------------------------------------------

test('the page does not list everything before a search', function () {
    $token = inventoryAgentToken();
    reportInventory($token, 'PC-001', chromeAndOffice())->assertOk();

    $this->actingAs(inventoryUser())
        ->get(route('software-inventory.index'))
        ->assertOk()
        ->assertSee('ติดตั้งมากที่สุด')
        ->assertSee('พิมพ์ในช่องค้นหา');
});

test('versions of one product collapse into a single row', function () {
    $token = inventoryAgentToken();

    foreach (['120.0', '121.0', '122.0'] as $i => $version) {
        reportInventory($token, 'PC-'.$i, [
            ['name' => 'Google Chrome', 'version' => $version, 'publisher' => 'Google LLC'],
        ], 'uuid-'.$i)->assertOk();
    }

    $response = $this->actingAs(inventoryUser())
        ->get(route('software-inventory.index', ['search' => 'chrome']))
        ->assertOk();

    // One product row carrying three versions across three machines, rather
    // than three rows that all say Chrome.
    $response->assertSee('Google Chrome');
    expect(substr_count($response->getContent(), 'Google Chrome'))->toBe(1);
});

test('the page costs the same number of queries whatever the fleet size', function () {
    $token = inventoryAgentToken();

    foreach (range(1, 3) as $i) {
        reportInventory($token, 'PC-'.$i, chromeAndOffice(), 'uuid-'.$i)->assertOk();
    }

    $user = inventoryUser();

    // Warm anything cached on first use — permissions especially — so the two
    // measurements compare the page and nothing else.
    $this->actingAs($user)->get(route('software-inventory.index', ['search' => 'chrome']))->assertOk();

    $count = function () use ($user) {
        $queries = 0;
        $listener = function () use (&$queries) {
            $queries++;
        };

        DB::listen($listener);

        $this->actingAs($user)
            ->get(route('software-inventory.index', ['search' => 'chrome']))
            ->assertOk();

        return $queries;
    };

    $withThree = $count();

    foreach (range(4, 20) as $i) {
        reportInventory($token, 'PC-'.$i, chromeAndOffice(), 'uuid-'.$i)->assertOk();
    }

    // The old controller loaded every machine's snapshot to render the page,
    // so this number grew with the fleet.
    expect($count())->toBe($withThree);
});

test('the drill-down lists the machines carrying a package', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', chromeAndOffice(), 'uuid-1')->assertOk();
    reportInventory($token, 'PC-002', [
        ['name' => 'Microsoft Office', 'version' => '2021', 'publisher' => 'Microsoft'],
    ], 'uuid-2')->assertOk();

    $this->actingAs(inventoryUser())
        ->get(route('software-inventory.computers', ['name' => 'Google Chrome']))
        ->assertOk()
        ->assertSee('PC-001')
        ->assertDontSee('PC-002');
});

test('the department filter narrows the results', function () {
    $token = inventoryAgentToken();
    $ward = Department::factory()->create();

    reportInventory($token, 'PC-001', chromeAndOffice(), 'uuid-1')->assertOk();
    reportInventory($token, 'PC-002', chromeAndOffice(), 'uuid-2')->assertOk();

    Computer::where('hostname', 'PC-001')->update(['department_id' => $ward->id]);

    $this->actingAs(inventoryUser())
        ->get(route('software-inventory.index', ['search' => 'chrome', 'department_id' => $ward->id]))
        ->assertOk()
        // Only the one machine in that ward should be counted.
        ->assertViewHas('products', fn ($products) => $products->first()->computer_count === 1);
});

test('a machine that stopped reporting drops out of the counts', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', chromeAndOffice(), 'uuid-1')->assertOk();
    reportInventory($token, 'PC-002', chromeAndOffice(), 'uuid-2')->assertOk();

    // One machine goes quiet for longer than the active window.
    Computer::where('hostname', 'PC-002')->update(['last_seen_at' => now()->subDays(60)]);

    $this->actingAs(inventoryUser())
        ->get(route('software-inventory.index', ['search' => 'chrome']))
        ->assertOk()
        ->assertViewHas('products', fn ($products) => $products->first()->computer_count === 1);
});

test('the inventory needs the software permission', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('software-inventory.index'))
        ->assertForbidden();
});

test('a package that reports no version is not duplicated on every report', function () {
    $token = inventoryAgentToken();

    // A third of a real machine's entries carry no version. The unique key
    // includes the version column, and NULL never equals NULL, so storing
    // these as null meant upsert inserted a fresh row every single report.
    $noVersion = [
        ['name' => 'ANTPDFReader', 'version' => '', 'publisher' => ''],
        ['name' => 'Attendance Management', 'publisher' => 'Vendor'],
    ];

    foreach (range(1, 4) as $ignored) {
        reportInventory($token, 'PC-001', $noVersion)->assertOk();
    }

    expect(ComputerSoftware::count())->toBe(2)
        ->and(ComputerSoftware::whereNull('version')->count())->toBe(0);
});

test('a versionless package is still removed once it is uninstalled', function () {
    $token = inventoryAgentToken();

    reportInventory($token, 'PC-001', [
        ['name' => 'ANTPDFReader', 'version' => ''],
        ['name' => 'Google Chrome', 'version' => '120.0'],
    ])->assertOk();

    reportInventory($token, 'PC-001', [
        ['name' => 'Google Chrome', 'version' => '120.0'],
    ])->assertOk();

    expect(ComputerSoftware::count())->toBe(1)
        ->and(ComputerSoftware::first()->normalized_name)->toBe('google chrome');
});
