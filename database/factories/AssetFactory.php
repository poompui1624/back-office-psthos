<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'asset_code' => fake()->unique()->numerify('AS######'),
            'name' => 'เครื่อง'.fake()->word(),
            'asset_category_id' => AssetCategory::factory(),
            'department_id' => Department::factory(),
            'responsible_employee_id' => null,
            'brand' => fake()->company(),
            'model' => fake()->bothify('MD-###??'),
            'serial_number' => fake()->unique()->bothify('SN########'),
            'received_date' => fake()->dateTimeBetween('-6 years', '-1 month')->format('Y-m-d'),
            'purchase_price' => fake()->numberBetween(5000, 500000),
            'budget_source' => fake()->randomElement(['เงินบำรุง', 'งบประมาณ', 'บริจาค']),
            'location' => fake()->word(),
            'status' => 'normal',
            'remark' => null,
        ];
    }

    public function receivedOn(string $date): static
    {
        return $this->state(fn () => ['received_date' => $date]);
    }

    public function inDepartment(Department $department): static
    {
        return $this->state(fn () => ['department_id' => $department->id]);
    }
}
