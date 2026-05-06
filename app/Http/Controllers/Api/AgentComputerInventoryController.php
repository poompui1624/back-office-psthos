<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Computer;
use App\Models\ComputerAgent;
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
                $computer = new Computer();
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

            $computer->snapshots()->create([
                'hostname' => $validated['hostname'],
                'ip_address' => $validated['ip_address'] ?? null,

                'os_name' => $validated['os_name'] ?? null,
                'os_version' => $validated['os_version'] ?? null,

                'cpu_name' => $validated['cpu_name'] ?? null,
                'ram_gb' => $validated['ram_gb'] ?? null,
                'storage_gb' => $validated['storage_gb'] ?? null,

                'installed_software' => $validated['installed_software'] ?? null,
                'raw_payload' => $validated['raw_payload'] ?? $validated,
                'reported_at' => now(),
            ]);

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
