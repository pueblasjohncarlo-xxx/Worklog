<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\WorkLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkLogFactory extends Factory
{
    protected $model = WorkLog::class;

    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'work_date' => now()->toDateString(),
            'hours' => 8,
            'description' => fake()->sentence(),
            'status' => 'draft',
            'grade' => null,
            'reviewer_comment' => null,
            'reviewer_id' => null,
            'reviewed_at' => null,
        ];
    }
}
