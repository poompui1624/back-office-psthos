<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SoftwareInventoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->can('software.view'), 403);

        $search = trim($request->string('search')->toString());

        $computers = Computer::query()
            ->with(['latestSnapshot'])
            ->whereHas('latestSnapshot')
            ->orderBy('hostname')
            ->get();

        $softwareItems = collect();

        foreach ($computers as $computer) {
            $snapshot = $computer->latestSnapshot;

            if (! $snapshot || ! is_array($snapshot->installed_software)) {
                continue;
            }

            foreach ($snapshot->installed_software as $software) {
                $name = trim($software['name'] ?? '');

                if ($name === '') {
                    continue;
                }

                $version = trim($software['version'] ?? '');
                $publisher = trim($software['publisher'] ?? '');

                if ($search !== '') {
                    $haystack = mb_strtolower($name . ' ' . $version . ' ' . $publisher);

                    if (! str_contains($haystack, mb_strtolower($search))) {
                        continue;
                    }
                }

                $key = mb_strtolower($name) . '|' . mb_strtolower($version) . '|' . mb_strtolower($publisher);

                if (! $softwareItems->has($key)) {
                    $softwareItems->put($key, [
                        'name' => $name,
                        'version' => $version,
                        'publisher' => $publisher,
                        'computer_count' => 0,
                        'computers' => [],
                    ]);
                }

                $item = $softwareItems->get($key);

                $item['computer_count']++;
                $item['computers'][] = [
                    'id' => $computer->id,
                    'hostname' => $computer->hostname,
                    'ip_address' => $computer->ip_address,
                ];

                $softwareItems->put($key, $item);
            }
        }

        $softwareItems = $softwareItems
            ->values()
            ->sortBy([
                ['name', 'asc'],
                ['version', 'asc'],
            ])
            ->values();

        $softwareItems = $this->paginateCollection(
            collection: $softwareItems,
            perPage: 30,
            request: $request
        );

        return view('software-inventory.index', compact('softwareItems', 'search'));
    }

    public function computers(Request $request)
    {
        abort_unless(auth()->user()->can('software.view'), 403);

        $name = trim($request->string('name')->toString());
        $version = trim($request->string('version')->toString());
        $publisher = trim($request->string('publisher')->toString());

        abort_if($name === '', 404);

        $computers = Computer::query()
            ->with(['latestSnapshot', 'department', 'responsibleEmployee'])
            ->whereHas('latestSnapshot')
            ->orderBy('hostname')
            ->get()
            ->filter(function ($computer) use ($name, $version, $publisher) {
                $snapshot = $computer->latestSnapshot;

                if (! $snapshot || ! is_array($snapshot->installed_software)) {
                    return false;
                }

                foreach ($snapshot->installed_software as $software) {
                    $softwareName = trim($software['name'] ?? '');
                    $softwareVersion = trim($software['version'] ?? '');
                    $softwarePublisher = trim($software['publisher'] ?? '');

                    if ($softwareName !== $name) {
                        continue;
                    }

                    if ($version !== '' && $softwareVersion !== $version) {
                        continue;
                    }

                    if ($publisher !== '' && $softwarePublisher !== $publisher) {
                        continue;
                    }

                    return true;
                }

                return false;
            })
            ->values();

        return view('software-inventory.computers', compact(
            'computers',
            'name',
            'version',
            'publisher'
        ));
    }

    private function paginateCollection(Collection $collection, int $perPage, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            items: $collection->forPage($page, $perPage),
            total: $collection->count(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
