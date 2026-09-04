<?php

namespace Database\Factories;

use App\Models\AttendanceDailySummary;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceDailySummary>
 */
class AttendanceDailySummaryFactory extends Factory
{
    protected $model = AttendanceDailySummary::class;

    public function definition(): array
    {
        $workDate = Carbon::parse(fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'));

        return [
            'employee_id' => Employee::factory(),
            'work_date' => $workDate->toDateString(),
            'first_in_at' => $workDate->copy()->setTime(8, 0),
            'last_out_at' => $workDate->copy()->setTime(16, 0),
            'expected_in_time' => '08:00:00',
            'expected_out_time' => '16:00:00',
            'work_minutes' => 480,
            'late_minutes' => 0,
            'early_leave_minutes' => 0,
            'status' => 'present',
            'remark' => null,
            'generated_at' => now(),
        ];
    }

    public function onDate(string $workDate): static
    {
        return $this->state(function () use ($workDate) {
            $date = Carbon::parse($workDate);

            return [
                'work_date' => $date->toDateString(),
                'first_in_at' => $date->copy()->setTime(8, 0),
                'last_out_at' => $date->copy()->setTime(16, 0),
            ];
        });
    }

    public function late(int $minutes = 30): static
    {
        return $this->state(fn () => [
            'status' => 'late',
            'late_minutes' => $minutes,
        ]);
    }

    public function earlyLeave(int $minutes = 30): static
    {
        return $this->state(fn () => ['early_leave_minutes' => $minutes]);
    }

    public function absent(): static
    {
        return $this->state(fn () => [
            'status' => 'absent',
            'first_in_at' => null,
            'last_out_at' => null,
            'work_minutes' => 0,
        ]);
    }

    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn () => ['employee_id' => $employee->id]);
    }
}
