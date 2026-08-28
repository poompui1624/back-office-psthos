<?php

namespace Database\Factories;

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItaMoitTopic>
 */
class ItaMoitTopicFactory extends Factory
{
    protected $model = ItaMoitTopic::class;

    public function definition(): array
    {
        return [
            'fiscal_year_id' => ItaFiscalYear::factory(),
            'indicator_no' => fake()->unique()->numberBetween(1, 99),
            'indicator_title' => 'ตัวชี้วัดที่ '.fake()->unique()->numberBetween(1, 9999),
            'code' => fake()->unique()->numerify('MOIT##'),
            'title' => 'หัวข้อ '.fake()->unique()->words(2, true),
            'description' => null,
            'sort_order' => fake()->numberBetween(1, 50),
            'is_active' => true,
        ];
    }
}
