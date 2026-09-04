<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Renders every authenticated GET page.
 *
 * This is the net under the UI rework: a mistyped variable in a Blade template
 * shows up here rather than the first time somebody opens the page. It asserts
 * that a page renders, not that it renders correctly.
 *
 * Written as one test that walks the routes rather than a Pest dataset,
 * because datasets are collected before the application boots and the route
 * table is not available yet at that point.
 */

/**
 * Routes that do not return a themed HTML page.
 *
 * @var array<int, string>
 */
const SMOKE_SKIP = [
    'exports.table',              // spreadsheet download
    'exports.dashboard-summary',  // spreadsheet download
    'attachments.download',       // file stream
    'ita.public.index',           // public page, outside the app shell
    'logout',
];

/**
 * Query strings for routes that are drill-downs rather than landing pages.
 *
 * software-inventory.computers deliberately 404s without a software name:
 * it answers "which machines have this installed", so there is nothing to
 * show without one.
 *
 * @var array<string, array<string, string>>
 */
const SMOKE_QUERY = [
    'software-inventory.computers' => ['name' => 'Microsoft Office'],
];

/**
 * Route parameters whose name does not match their model class.
 *
 * The ITA routes are nested under an /ita prefix, so their parameters drop
 * the prefix the model keeps.
 *
 * @var array<string, string>
 */
const SMOKE_MODEL_ALIAS = [
    // The public-site routes sit under a /site prefix, so their parameters
    // drop the prefix the model keeps.
    'banner' => 'SiteBanner',
    'post' => 'SitePost',
    'link' => 'SiteLink',
    'page' => 'SitePage',
    'executive' => 'SiteExecutive',

    'document' => 'ItaDocument',
    'fiscal_year' => 'ItaFiscalYear',
    'moit_topic' => 'ItaMoitTopic',
    'moit_sub_topic' => 'ItaMoitSubTopic',
];

/**
 * Build the model a route parameter needs, reusing the project factories.
 */
function smokeParameter(string $parameter): ?object
{
    // Derived from User::class rather than written out, so the namespace
    // stays correct if the models ever move.
    $namespace = Str::beforeLast(User::class, chr(92)).chr(92);

    $class = SMOKE_MODEL_ALIAS[$parameter] ?? Str::studly(Str::singular($parameter));

    $model = $namespace.$class;

    if (! class_exists($model)) {
        return null;
    }

    if ($existing = $model::query()->first()) {
        return $existing;
    }

    if (! method_exists($model, 'factory')) {
        return null;
    }

    try {
        return $model::factory()->create();
    } catch (Throwable) {
        return null;
    }
}

test('every authenticated page renders', function () {
    $this->seed(RolePermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $failures = [];
    $skipped = [];
    $checked = 0;

    foreach (Route::getRoutes() as $route) {
        $name = $route->getName();

        if (! $name
            || ! in_array('GET', $route->methods(), true)
            || in_array($name, SMOKE_SKIP, true)
            || ! in_array('auth', $route->gatherMiddleware(), true)
            || Str::startsWith($route->uri(), ['livewire', '_', 'storage'])) {
            continue;
        }

        $parameters = [];
        $missing = null;

        foreach ($route->parameterNames() as $parameter) {
            $model = smokeParameter($parameter);

            if ($model === null) {
                $missing = $parameter;
                break;
            }

            // getRouteKey, not getKey: a model binding on something other
            // than its primary key — SitePage binds on its key column —
            // would otherwise be sent an id the route cannot resolve.
            $parameters[$parameter] = $model->getRouteKey();
        }

        if ($missing !== null) {
            $skipped[] = "{$name} (no factory for '{$missing}')";

            continue;
        }

        $checked++;

        try {
            $query = SMOKE_QUERY[$name] ?? [];

            $response = $this->actingAs($admin)->get(route($name, $parameters + $query));
        } catch (Throwable $e) {
            $failures[] = "{$name}: ".$e->getMessage();

            continue;
        }

        if (! in_array($response->status(), [200, 302], true)) {
            $failures[] = "{$name}: HTTP {$response->status()} at ".route($name, $parameters + $query);

            continue;
        }

        $body = $response->getContent();

        foreach (['Undefined variable', 'htmlspecialchars(): Argument', 'Undefined array key'] as $symptom) {
            if (is_string($body) && str_contains($body, $symptom)) {
                $failures[] = "{$name}: {$symptom}";
            }
        }
    }

    if ($skipped !== []) {
        // Surfaced, not failed: a module without a factory is a coverage gap
        // to close, not a broken page.
        fwrite(STDERR, "\n  smoke skipped ".count($skipped)." route(s):\n    ".implode("\n    ", $skipped)."\n");
    }

    expect($checked)->toBeGreaterThan(60)
        ->and($failures)->toBe([]);
});
