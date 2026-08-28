<?php

namespace Database\Factories;

use App\Models\SoftwareProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SoftwareProduct>
 */
class SoftwareProductFactory extends Factory
{
    protected $model = SoftwareProduct::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'vendor' => fake()->company(),
            'category' => fake()->randomElement(['os', 'office', 'security', 'utility']),
            'description' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
