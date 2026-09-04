<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Computer;
use App\Models\ComputerAgent;
use App\Models\ComputerSoftware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentComputerInventoryController extends Controller
{
    public function store(Request $request)
    {
        $agent = $this->authenticateAgent($request);

        if (! $agent) {
            return response()->json([
                'message' => 'Unauthorized agent',
            ], 401);
        }

        $validated = $request->validate([
            'machine_uuid' => ['nullable', 'string', 'max:255'],
            'hostname' => ['required', 'string', 'max:255'],

            'ip_address' => ['nullable', 'string', 'max:50'],
            'mac_address' => ['nullable', 'string', 'max:100'],

            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],

            'os_name' => ['nullable', 'string', 'max:255'],
            'os_version' => ['nullable', 'string', 'max:255'],

            'cpu_name' => ['nullable', 'string', 'max:255'],
            'ram_gb' => ['nullable', 'integer', 'min:0'],
            'storage_gb' => ['nullable', 'integer', 'min:0'],

            'installed_software' => ['nullable', 'array'],
            'raw_payload' => ['nullable', 'array'],
        ]);

        $computer = DB::transaction(function () use ($validated) {
            $computer = $this->findComputer($validated);

            if (! $computer) {
                $computer = new Computer;
            }

            $computer->fill([
                'machine_uuid' => $validated['machine_uuid'] ?? $computer->machine_uuid,
                'hostname' => $validated['hostname'],
                'ip_address' => $validated['ip_address'] ?? null,
                'mac_address' => $validated['mac_address'] ?? $computer->mac_address,

                'manufacturer' => $validated['manufacturer'] ?? null,
                'model' => $validated['model'] ?? null,
                'serial_number' => $validated['serial_number'] ?? null,

                'os_name' => $validated['os_name'] ?? null,
                'os_version' => $validated['os_version'] ?? null,

                'cpu_name' => $validated['cpu_name'] ?? null,
                'ram_gb' => $validated['ram_gb'] ?? null,
                'storage_gb' => $validated['storage_gb'] ?? null,

                'last_seen_at' => now(),
                'source' => 'agent',
                'status' => 'active',
            ]);

            $computer->save();

            $software = $validated['installed_software'] ?? [];
            $fingerprint = ComputerSoftware::fingerprint($software);

            // Agents report daily but software changes perhaps twice a month.
            // Writing a snapshot each time stored the same 16 KB of JSON over
            // and over, so an unchanged list only refreshes what was seen.
            $unchanged = $computer->snapshots()
                ->where('software_hash', $fingerprint)
                ->latest('reported_at')
                ->exists();

            if (! $unchanged) {
                $computer->snapshots()->create([
                    'hostname' => $validated['hostname'],
                    'ip_address' => $validated['ip_address'] ?? null,

                    'os_name' => $validated['os_name'] ?? null,
                    'os_version' => $validated['os_version'] ?? null,

                    'cpu_name' => $validated['cpu_name'] ?? null,
                    'ram_gb' => $validated['ram_gb'] ?? null,
                    'storage_gb' => $validated['storage_gb'] ?? null,

                    'installed_software' => $software,
                    'software_hash' => $fingerprint,
                    'raw_payload' => $validated['raw_payload'] ?? $validated,
                    'reported_at' => now(),
                ]);
            }

            $this->syncSoftware($computer, $software);

            return $computer;
        });

        $agent->update([
            'last_seen_at' => now(),
            'last_ip_address' => $request->ip(),
            'last_user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Inventory received',
            'computer_id' => $computer->id,
            'hostname' => $computer->hostname,
        ]);
    }

    /**
     * Mirror the reported list into computer_software.
     *
     * computer_snapshots holds the history as JSON, which cannot be queried;
     * this table is what the inventory page reads, so it has to reflect what
     * is on the machine right now — including packages that were uninstalled
     * since the last report.
     *
     * @param  array<int, array<string, mixed>>  $installedSoftware
     */
    private function syncSoftware(Computer $computer, array $installedSoftware): void
    {
        $now = now();
        $rows = [];

        foreach (ComputerSoftware::asEntryList($installedSoftware) as $item) {
            $name = trim((string) ($item['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $version = trim((string) ($item['version'] ?? ''));
            $normalized = ComputerSoftware::normalizeName($name);

            // The unique key is (computer, normalized_name, version), so a
            // machine reporting the same package twice would otherwise make
            // upsert fail on a duplicate within its own batch.
            $rows[$normalized.'|'.$version] = [
                'computer_id' => $computer->id,
                'name' => $name,
                'normalized_name' => $normalized,
                // Empty rather than null: the unique key includes this column,
                // and NULL never equals NULL, so nulls would not deduplicate.
                'version' => $version,
                'publisher' => trim((string) ($item['publisher'] ?? '')) ?: null,
                'is_component' => ComputerSoftware::looksLikeComponent($name),
                'first_seen_at' => $now,
                'last_seen_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Read what is on record before writing, so the rows to remove can be
        // identified by primary key. Comparing last_seen_at instead would miss
        // packages when two reports land in the same second, because the
        // column stores no fractional seconds.
        $existing = ComputerSoftware::query()
            ->where('computer_id', $computer->id)
            ->get(['id', 'normalized_name', 'version']);

        if ($rows !== []) {
            // first_seen_at is deliberately absent from the update list: a
            // package already known keeps the date it was first reported.
            ComputerSoftware::upsert(
                array_values($rows),
                ['computer_id', 'normalized_name', 'version'],
                ['name', 'publisher', 'is_component', 'last_seen_at', 'updated_at']
            );
        }

        // Anything on record but absent from this report has been uninstalled.
        $removed = $existing
            ->reject(fn ($row) => isset($rows[$row->normalized_name.'|'.((string) $row->version)]))
            ->pluck('id');

        if ($removed->isNotEmpty()) {
            ComputerSoftware::whereKey($removed)->delete();
        }
    }

    private function authenticateAgent(Request $request): ?ComputerAgent
    {
        $token = $request->bearerToken();

        if (! $token) {
            return null;
        }

        return ComputerAgent::query()
            ->where('token_hash', ComputerAgent::hashToken($token))
            ->where('is_active', true)
            ->first();
    }

    private function findComputer(array $data): ?Computer
    {
        if (! empty($data['machine_uuid'])) {
            $computer = Computer::where('machine_uuid', $data['machine_uuid'])->first();

            if ($computer) {
                return $computer;
            }
        }

        if (! empty($data['mac_address'])) {
            $computer = Computer::where('mac_address', $data['mac_address'])->first();

            if ($computer) {
                return $computer;
            }
        }

        return Computer::where('hostname', $data['hostname'])->first();
    }
}
