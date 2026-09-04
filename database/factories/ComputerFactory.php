<?php

namespace Database\Factories;

use App\Models\Computer;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Computer>
 */
class ComputerFactory extends Factory
{
    protected $model = Computer::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'machine_uuid' => fake()->unique()->uuid(),
            'hostname' => fake()->unique()->bothify('PC-####'),
            'ip_address' => fake()->localIpv4(),
            'mac_address' => fake()->macAddress(),
            'manufacturer' => fake()->randomElement(['Dell', 'HP', 'Lenovo', 'Acer']),
            'model' => fake()->bothify('Model-###'),
            'serial_number' => fake()->unique()->bothify('SN########'),
            'os_name' => 'Windows 11 Pro',
            'os_version' => '23H2',
            'cpu_name' => 'Intel Core i5',
            'ram_gb' => fake()->randomElement([8, 16, 32]),
            'storage_gb' => fake()->randomElement([256, 512, 1024]),
            'last_seen_at' => now(),
            'source' => 'manual',
            'status' => 'active',
            'remark' => null,
        ];
    }

    public function inDepartment(Department $department): static
    {
        return $this->state(fn () => ['department_id' => $department->id]);
    }
}
