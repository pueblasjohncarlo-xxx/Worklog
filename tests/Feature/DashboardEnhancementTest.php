<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Company;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardEnhancementTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_can_see_student_progress_and_recent_activity()
    {
        $coordinator = User::create([
            'name' => 'Coordinator',
            'email' => 'coord@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
        ]);

        $student = User::create([
            'name' => 'Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $supervisor = User::create([
            'name' => 'Supervisor',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
        ]);

        $company = Company::create([
            'name' => 'Test Company',
        ]);

        $assignment = Assignment::create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'company_id' => $company->id,
            'required_hours' => 100,
            'status' => 'active',
        ]);

        // Create some approved worklogs
        WorkLog::create([
            'assignment_id' => $assignment->id,
            'work_date' => now(),
            'hours' => 25,
            'status' => 'approved',
            'description' => 'Work 1',
        ]);

        WorkLog::create([
            'assignment_id' => $assignment->id,
            'work_date' => now()->subDay(),
            'hours' => 25,
            'status' => 'approved',
            'description' => 'Work 2',
        ]);

        $response = $this->actingAs($coordinator)->get(route('coordinator.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Student Progress Overview');
        $response->assertSee('Recent Activity');
        $response->assertSee($student->name);
        $response->assertSee('50%'); // 50/100 hours
        $response->assertSee('50'); // Hours completed
    }

    public function test_supervisor_can_see_assigned_student_progress()
    {
        $supervisor = User::create([
            'name' => 'Supervisor',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
        ]);

        $student = User::create([
            'name' => 'Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $company = Company::create([
            'name' => 'Test Company',
        ]);

        $assignment = Assignment::create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'company_id' => $company->id,
            'required_hours' => 200,
            'status' => 'active',
        ]);

        WorkLog::create([
            'assignment_id' => $assignment->id,
            'work_date' => now(),
            'hours' => 20,
            'status' => 'approved',
            'description' => 'Work',
        ]);

        $response = $this->actingAs($supervisor)->get(route('supervisor.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($student->name);
        $response->assertSee('10%'); // 20/200 hours
        $response->assertSee('20'); // Hours completed
    }

    public function test_student_can_see_own_progress()
    {
        $student = User::create([
            'name' => 'Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $supervisor = User::create([
            'name' => 'Supervisor',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
        ]);

        $company = Company::create([
            'name' => 'Test Company',
        ]);

        $assignment = Assignment::create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'company_id' => $company->id,
            'required_hours' => 100,
            'status' => 'active',
        ]);

        WorkLog::create([
            'assignment_id' => $assignment->id,
            'work_date' => now(),
            'hours' => 75,
            'status' => 'approved',
            'description' => 'Work',
        ]);

        $response = $this->actingAs($student)->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Progress');
        $response->assertSee('75%'); // 75/100 hours
        $response->assertSee('75'); // Hours completed
    }

    public function test_audit_logs_are_created()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $this->actingAs($user);

        $company = Company::create([
            'name' => 'Audit Company',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'auditable_type' => Company::class,
            'auditable_id' => $company->id,
            'user_id' => $user->id,
        ]);

        $company->update(['name' => 'Updated Company']);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'updated',
            'auditable_type' => Company::class,
            'auditable_id' => $company->id,
            'user_id' => $user->id,
        ]);
    }
}
