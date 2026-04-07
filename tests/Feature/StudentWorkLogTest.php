<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Company;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentWorkLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_see_dashboard_with_assignment_and_worklogs(): void
    {
        $student = User::create([
            'name' => 'Student',
            'email' => 'student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $company = Company::factory()->create();

        $assignment = Assignment::factory()->create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'supervisor_id' => User::factory()->create(['role' => User::ROLE_SUPERVISOR])->id,
            'status' => 'active',
        ]);

        WorkLog::factory()->create([
            'assignment_id' => $assignment->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($student)->get('/student/dashboard');

        $response->assertOk();
        $response->assertSee('Work Logs');
    }

    public function test_student_can_create_and_submit_worklog(): void
    {
        $student = User::create([
            'name' => 'Student',
            'email' => 'student2@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $company = Company::factory()->create();

        Assignment::factory()->create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'supervisor_id' => User::factory()->createOne(['role' => User::ROLE_SUPERVISOR])->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($student)->post('/student/worklogs', [
            'work_date' => now()->format('Y-m-d'),
            'hours' => 8,
            'description' => 'Did tasks',
        ]);

        $response->assertRedirect('/student/dashboard');

        $workLog = WorkLog::first();

        $this->assertNotNull($workLog);
        $this->assertEquals('draft', $workLog->status);

        $response = $this->actingAs($student)->post("/student/worklogs/{$workLog->id}/submit");

        $response->assertRedirect('/student/dashboard');

        $this->assertEquals('submitted', $workLog->fresh()->status);
    }
}
