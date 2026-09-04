<?php

namespace Database\Factories;

use App\Models\ItaFiscalYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItaFiscalYear>
 */
class ItaFiscalYearFactory extends Factory
{
    protected $model = ItaFiscalYear::class;

    public function definition(): array
    {
        return [
            'year' => fake()->unique()->numberBetween(2560, 2600),
            'name' => fn (array $attributes) => 'ปีงบประมาณ '.$attributes['year'],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
