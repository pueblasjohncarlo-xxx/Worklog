<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoordinatorFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_can_create_company_and_assignment(): void
    {
        $coordinator = User::create([
            'name' => 'Coordinator',
            'email' => 'coordinator@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
        ]);

        $response = $this->actingAs($coordinator)->post('/coordinator/companies', [
            'name' => 'Test Company',
        ]);

        $response->assertRedirect('/coordinator/companies');

        $company = Company::first();

        $this->assertNotNull($company);

        $student = User::create([
            'name' => 'Student',
            'email' => 'student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $supervisor = User::create([
            'name' => 'Supervisor',
            'email' => 'supervisor@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
        ]);

        $response = $this->actingAs($coordinator)->post('/coordinator/assignments', [
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'company_id' => $company->id,
        ]);

        $response->assertRedirect('/coordinator/assignments');

        $this->assertDatabaseHas('assignments', [
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'company_id' => $company->id,
        ]);
    }
}
