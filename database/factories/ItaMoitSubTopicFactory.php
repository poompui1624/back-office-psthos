<?php

namespace Database\Factories;

use App\Models\ItaFiscalYear;
use App\Models\ItaMoitSubTopic;
use App\Models\ItaMoitTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItaMoitSubTopic>
 */
class ItaMoitSubTopicFactory extends Factory
{
    protected $model = ItaMoitSubTopic::class;

    public function definition(): array
    {
        return [
            'fiscal_year_id' => ItaFiscalYear::factory(),
            'main_topic_id' => ItaMoitTopic::factory(),
            'code' => fake()->unique()->numerify('MOIT##.#'),
            'title' => 'หัวข้อย่อย '.fake()->unique()->words(2, true),
            'description' => null,
            'sort_order' => fake()->numberBetween(1, 50),
            'is_active' => true,
        ];
    }
}
