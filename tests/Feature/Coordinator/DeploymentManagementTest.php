<?php

namespace Tests\Feature\Coordinator;

use App\Models\Assignment;
use App\Models\Company;
use App\Models\SupervisorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeploymentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_can_update_a_deployment_record(): void
    {
        $coordinator = User::create([
            'name' => 'Coordinator User',
            'email' => 'coordinator@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $company = Company::create([
            'name' => 'Original Company',
        ]);

        $updatedCompany = Company::create([
            'name' => 'Updated Company',
        ]);

        $originalSupervisor = User::create([
            'name' => 'Original Supervisor',
            'email' => 'original-supervisor@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        SupervisorProfile::create([
            'user_id' => $originalSupervisor->id,
            'company_id' => $company->id,
            'position_title' => 'Supervisor',
            'department' => 'IT',
            'phone' => '555-0999',
        ]);

        $supervisor = User::create([
            'name' => 'Supervisor User',
            'email' => 'supervisor@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        SupervisorProfile::create([
            'user_id' => $supervisor->id,
            'company_id' => $updatedCompany->id,
            'position_title' => 'Supervisor',
            'department' => 'IT',
            'phone' => '555-1000',
        ]);

        $adviser = User::create([
            'name' => 'Adviser User',
            'email' => 'adviser@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_OJT_ADVISER,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $assignment = Assignment::create([
            'student_id' => $student->id,
            'supervisor_id' => $originalSupervisor->id,
            'ojt_adviser_id' => null,
            'coordinator_id' => $coordinator->id,
            'company_id' => $company->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-01',
            'status' => 'active',
            'required_hours' => 1600,
        ]);

        $response = $this->actingAs($coordinator)->patch(route('coordinator.deployment.update', $assignment), [
            'supervisor_id' => $supervisor->id,
            'ojt_adviser_id' => $adviser->id,
            'company_id' => $updatedCompany->id,
            'start_date' => '2026-04-01',
            'end_date' => '2026-05-01',
            'required_hours' => 1800,
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('coordinator.deployment.index'));

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'ojt_adviser_id' => $adviser->id,
            'company_id' => $updatedCompany->id,
            'status' => 'completed',
            'required_hours' => 1800,
        ]);

        $assignment->refresh();

        $this->assertSame('2026-04-01', $assignment->start_date?->format('Y-m-d'));
        $this->assertSame('2026-05-01', $assignment->end_date?->format('Y-m-d'));
        $this->assertSame($student->id, $assignment->student_id);
    }

    public function test_coordinator_cannot_save_a_deployment_with_a_mismatched_company(): void
    {
        $coordinator = User::create([
            'name' => 'Coordinator User',
            'email' => 'coordinator2@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $student = User::create([
            'name' => 'Student User',
            'email' => 'student2@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $company = Company::create([
            'name' => 'Company A',
        ]);

        $otherCompany = Company::create([
            'name' => 'Company B',
        ]);

        $supervisor = User::create([
            'name' => 'Supervisor User',
            'email' => 'supervisor2@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        SupervisorProfile::create([
            'user_id' => $supervisor->id,
            'company_id' => $company->id,
            'position_title' => 'Supervisor',
            'department' => 'IT',
            'phone' => '555-2000',
        ]);

        $assignment = Assignment::create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'ojt_adviser_id' => null,
            'coordinator_id' => $coordinator->id,
            'company_id' => $company->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-01',
            'status' => 'active',
            'required_hours' => 1600,
        ]);

        $response = $this->actingAs($coordinator)->patch(route('coordinator.deployment.update', $assignment), [
            'supervisor_id' => $supervisor->id,
            'company_id' => $otherCompany->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('company_id');

        $assignment->refresh();
        $this->assertSame($company->id, $assignment->company_id);
        $this->assertSame($supervisor->id, $assignment->supervisor_id);
    }
}