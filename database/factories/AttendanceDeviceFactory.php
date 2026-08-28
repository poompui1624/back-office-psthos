<?php

namespace Database\Factories;

use App\Models\AttendanceDevice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceDevice>
 */
class AttendanceDeviceFactory extends Factory
{
    protected $model = AttendanceDevice::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('DEV###'),
            'name' => 'เครื่องสแกน '.fake()->unique()->numberBetween(1, 9999),
            'location' => 'อาคาร '.fake()->randomElement(['A', 'B', 'C']),
            'ip_address' => fake()->localIpv4(),
            'brand' => fake()->randomElement(['ZKTeco', 'Hikvision', 'Suprema']),
            'model' => fake()->bothify('MD-###'),
            'is_active' => true,
            'remark' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
