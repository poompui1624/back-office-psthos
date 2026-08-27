<?php

namespace Database\Factories;

use App\Models\PayrollPeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollPeriod>
 */
class PayrollPeriodFactory extends Factory
{
    protected $model = PayrollPeriod::class;

    /**
     * How many months back the next generated period sits.
     *
     * payroll_periods is unique on (year, month), so each period built without an
     * explicit month walks one month further back instead of colliding.
     */
    protected static int $monthsBack = 0;

    public function definition(): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(static::$monthsBack++);

        return [
            'year' => $start->year,
            'month' => $start->month,
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->endOfMonth()->toDateString(),
            'name' => 'เงินเดือน '.$start->format('m/Y'),
            'status' => 'draft',
            'created_by' => User::factory(),
            'remark' => null,
        ];
    }

    public function forMonth(int $year, int $month): static
    {
        return $this->state(function () use ($year, $month) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();

            return [
                'year' => $year,
                'month' => $month,
                'start_date' => $start->toDateString(),
                'end_date' => $start->copy()->endOfMonth()->toDateString(),
                'name' => 'เงินเดือน '.$start->format('m/Y'),
            ];
        });
    }

    public function generated(): static
    {
        return $this->state(fn () => [
            'status' => 'generated',
            'generated_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => 'closed',
            'generated_at' => now(),
            'closed_at' => now(),
        ]);
    }
}
