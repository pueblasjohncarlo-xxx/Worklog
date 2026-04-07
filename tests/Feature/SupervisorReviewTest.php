<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Company;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisorReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_review_submitted_worklog(): void
    {
        $supervisor = User::create([
            'name' => 'Supervisor',
            'email' => 'supervisor@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
        ]);

        $student = User::create([
            'name' => 'Student',
            'email' => 'student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $company = Company::factory()->create();

        $assignment = Assignment::factory()->create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'company_id' => $company->id,
        ]);

        $workLog = WorkLog::factory()->create([
            'assignment_id' => $assignment->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($supervisor)->post("/supervisor/worklogs/{$workLog->id}/review", [
            'status' => 'approved',
            'grade' => 'A',
            'reviewer_comment' => 'Good work',
        ]);

        $response->assertRedirect('/supervisor/dashboard');

        $workLog->refresh();

        $this->assertEquals('approved', $workLog->status);
        $this->assertEquals('A', $workLog->grade);
        $this->assertEquals('Good work', $workLog->reviewer_comment);
        $this->assertNotNull($workLog->reviewed_at);
    }
}
