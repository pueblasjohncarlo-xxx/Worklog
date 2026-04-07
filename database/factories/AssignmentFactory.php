<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        $company = Company::factory()->create();
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $supervisor = User::factory()->create(['role' => User::ROLE_SUPERVISOR]);

        return [
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'coordinator_id' => null,
            'company_id' => $company->id,
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'status' => 'active',
        ];
    }
}
