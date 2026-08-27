<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\DutySchedule;
use App\Models\Employee;
use App\Models\ShiftType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DutySchedule>
 */
class DutyScheduleFactory extends Factory
{
    protected $model = DutySchedule::class;

    public function definition(): array
    {
        $workDate = Carbon::parse(fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'));

        return [
            'employee_id' => Employee::factory(),
            'department_id' => fn (array $attributes) => Employee::find($attributes['employee_id'])?->department_id,
            'shift_type_id' => ShiftType::factory(),
            'work_date' => $workDate->toDateString(),
            'start_at' => $workDate->copy()->setTime(8, 0),
            'end_at' => $workDate->copy()->setTime(16, 0),
            'role_group' => fake()->randomElement(['พยาบาล', 'ผู้ช่วยเหลือคนไข้', 'เวรเปล']),
            'status' => 'assigned',
            'remark' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }

    public function onDate(string $workDate): static
    {
        return $this->state(function () use ($workDate) {
            $date = Carbon::parse($workDate);

            return [
                'work_date' => $date->toDateString(),
                'start_at' => $date->copy()->setTime(8, 0),
                'end_at' => $date->copy()->setTime(16, 0),
            ];
        });
    }

    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn () => [
            'employee_id' => $employee->id,
            'department_id' => $employee->department_id,
        ]);
    }

    public function inDepartment(Department $department): static
    {
        return $this->state(fn () => ['department_id' => $department->id]);
    }
}
