<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\ComputerSoftware;
use App\Models\Department;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The software inventory, read from computer_software.
 *
 * This used to load every machine's snapshot JSON and group it in PHP on each
 * request, which at three hundred machines meant decoding several megabytes to
 * render thirty rows. The agent now writes a queryable row per package, so the
 * work here is a grouped query the database can index.
 */
class SoftwareInventoryController extends Controller
{
    /**
     * Machines quieter than this are treated as gone and left out of the counts.
     */
    private const ACTIVE_WITHIN_DAYS = 30;

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('software.view'), 403);

        $search = trim($request->string('search')->toString());
        $departmentId = $request->string('department_id')->toString();
        $publisher = trim($request->string('publisher')->toString());
        $includeComponents = $request->boolean('include_components');

        $hasQuery = $search !== '' || $departmentId !== '' || $publisher !== '';

        return view('software-inventory.index', [
            'search' => $search,
            'departmentId' => $departmentId,
            'publisher' => $publisher,
            'includeComponents' => $includeComponents,
            'hasQuery' => $hasQuery,

            'departments' => Department::query()
                ->where('is_active', true)
                ->orderBy('code')
                ->get(),

            'publishers' => $this->publishers($includeComponents),
            'summary' => $this->summary($includeComponents),

            // The page is built for looking something up rather than browsing,
            // so the full list is not rendered until a filter is set.
            'products' => $hasQuery
                ? $this->products($search, $departmentId, $publisher, $includeComponents)
                : null,

            'topProducts' => $hasQuery
                ? collect()
                : $this->products('', '', '', $includeComponents, 10),
        ]);
    }

    /**
     * The machines carrying one package, optionally pinned to a single version.
     */
    public function computers(Request $request): View
    {
        abort_unless(auth()->user()->can('software.view'), 403);

        $name = trim($request->string('name')->toString());
        $version = trim($request->string('version')->toString());
        $publisher = trim($request->string('publisher')->toString());

        abort_if($name === '', 404);

        $computers = Computer::query()
            ->with(['department', 'responsibleEmployee'])
            ->whereHas('software', function ($query) use ($name, $version, $publisher) {
                $query->where('normalized_name', ComputerSoftware::normalizeName($name));

                if ($version !== '') {
                    $query->where('version', $version);
                }

                if ($publisher !== '') {
                    $query->where('publisher', $publisher);
                }
            })
            ->orderBy('hostname')
            ->paginate(50)
            ->withQueryString();

        return view('software-inventory.computers', compact('computers', 'name', 'version', 'publisher'));
    }

    /**
     * One row per product, with how many versions and machines carry it.
     */
    private function products(
        string $search,
        string $departmentId,
        string $publisher,
        bool $includeComponents,
        ?int $limit = null
    ) {
        $query = ComputerSoftware::query()
            ->excludingComponents($includeComponents)
            ->onActiveComputers(self::ACTIVE_WITHIN_DAYS)
            ->select([
                DB::raw('MIN(computer_software.name) as name'),
                'computer_software.normalized_name',
                DB::raw('COUNT(DISTINCT computer_software.version) as version_count'),
                DB::raw('COUNT(DISTINCT computer_software.computer_id) as computer_count'),
                DB::raw('MIN(computer_software.publisher) as publisher'),
                DB::raw('MAX(computer_software.last_seen_at) as last_seen_at'),
            ])
            ->groupBy('computer_software.normalized_name')
            ->orderByDesc('computer_count')
            ->orderBy('name');

        if ($search !== '') {
            $query->where('computer_software.normalized_name', 'like', '%'.ComputerSoftware::normalizeName($search).'%');
        }

        if ($publisher !== '') {
            $query->where('computer_software.publisher', $publisher);
        }

        if ($departmentId !== '') {
            $query->whereHas('computer', fn ($computer) => $computer->where('department_id', $departmentId));
        }

        if ($limit !== null) {
            return $query->limit($limit)->get();
        }

        return $query->paginate(30)->withQueryString();
    }

    /**
     * The versions of one product and how many machines run each.
     *
     * @return Collection<int, object>
     */
    public function versionsFor(string $normalizedName, bool $includeComponents = false)
    {
        return ComputerSoftware::query()
            ->excludingComponents($includeComponents)
            ->onActiveComputers(self::ACTIVE_WITHIN_DAYS)
            ->where('normalized_name', $normalizedName)
            ->select([
                'version',
                DB::raw('COUNT(DISTINCT computer_id) as computer_count'),
            ])
            ->groupBy('version')
            ->orderByDesc('computer_count')
            ->get();
    }

    /**
     * @return array{products: int, installs: int, computers: int, last_report: Carbon|null}
     */
    private function summary(bool $includeComponents): array
    {
        $base = ComputerSoftware::query()
            ->excludingComponents($includeComponents)
            ->onActiveComputers(self::ACTIVE_WITHIN_DAYS);

        $totals = (clone $base)
            ->selectRaw('COUNT(DISTINCT normalized_name) as products')
            ->selectRaw('COUNT(*) as installs')
            ->selectRaw('COUNT(DISTINCT computer_id) as computers')
            ->first();

        return [
            'products' => (int) ($totals->products ?? 0),
            'installs' => (int) ($totals->installs ?? 0),
            'computers' => (int) ($totals->computers ?? 0),
            'last_report' => Computer::max('last_seen_at')
                ? Carbon::parse(Computer::max('last_seen_at'))
                : null,
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function publishers(bool $includeComponents)
    {
        return ComputerSoftware::query()
            ->excludingComponents($includeComponents)
            ->onActiveComputers(self::ACTIVE_WITHIN_DAYS)
            ->whereNotNull('publisher')
            ->where('publisher', '!=', '')
            ->distinct()
            ->orderBy('publisher')
            ->pluck('publisher');
    }
}
