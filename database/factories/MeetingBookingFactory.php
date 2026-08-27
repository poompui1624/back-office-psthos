<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\MeetingBooking;
use App\Models\MeetingRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingBooking>
 */
class MeetingBookingFactory extends Factory
{
    protected $model = MeetingBooking::class;

    public function definition(): array
    {
        $start = Carbon::parse(fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d H:00:00'));

        return [
            'booking_no' => 'MR'.fake()->unique()->numerify('##########'),
            'meeting_room_id' => MeetingRoom::factory(),
            'employee_id' => Employee::factory(),
            'department_id' => fn (array $attributes) => Employee::find($attributes['employee_id'])?->department_id,
            'created_by' => User::factory(),
            'title' => 'ประชุม'.fake()->word(),
            'purpose' => fake()->sentence(),
            'start_at' => $start,
            'end_at' => $start->copy()->addHours(2),
            'attendees_count' => fake()->numberBetween(3, 30),
            'need_projector' => false,
            'need_sound_system' => false,
            'need_video_conference' => false,
            'need_whiteboard' => false,
            'status' => 'pending',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'rejected_by' => User::factory(),
            'rejected_at' => now(),
            'approval_remark' => fake()->sentence(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Pin the booking to an exact slot, e.g. to build an overlap case.
     */
    public function slot(string $startAt, string $endAt): static
    {
        return $this->state(fn () => [
            'start_at' => Carbon::parse($startAt),
            'end_at' => Carbon::parse($endAt),
        ]);
    }
}
