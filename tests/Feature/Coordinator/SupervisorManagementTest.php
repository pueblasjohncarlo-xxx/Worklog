<?php

namespace Tests\Feature\Coordinator;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisorManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $coordinator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = User::create([
            'name' => 'Coordinator User',
            'email' => 'coord@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
        ]);
    }

    public function test_coordinator_can_access_add_supervisor_page()
    {
        $response = $this->actingAs($this->coordinator)->get(route('coordinator.supervisors.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Supervisor');
    }

    public function test_coordinator_can_create_supervisor_only()
    {
        $data = [
            'name' => 'Supervisor One',
            'email' => 's1@example.com',
            'phone' => '123456789',
            'position_title' => 'Senior Developer',
            'department' => 'IT',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'create_company' => '0',
        ];

        $response = $this->actingAs($this->coordinator)->post(route('coordinator.supervisors.store'), $data);

        $response->assertRedirect(route('coordinator.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 's1@example.com', 'role' => User::ROLE_SUPERVISOR]);
        $this->assertDatabaseHas('supervisor_profiles', [
            'position_title' => 'Senior Developer',
            'department' => 'IT',
            'company_id' => null,
        ]);
    }

    public function test_coordinator_can_create_supervisor_and_company_simultaneously()
    {
        $data = [
            'name' => 'Supervisor Two',
            'email' => 's2@example.com',
            'phone' => '987654321',
            'position_title' => 'Project Manager',
            'department' => 'Operations',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'create_company' => '1',
            'company_name' => 'New Tech Corp',
            'company_industry' => 'Tech',
            'company_address' => '123 Tech St',
            'company_city' => 'Tech City',
            'company_state' => 'Tech State',
            'company_postal_code' => '12345',
            'company_country' => 'Tech Country',
            'company_contact_person' => 'John Doe',
            'company_contact_email' => 'contact@newtech.com',
            'company_contact_phone' => '555-0199',
        ];

        $response = $this->actingAs($this->coordinator)->post(route('coordinator.supervisors.store'), $data);

        $response->assertRedirect(route('coordinator.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 's2@example.com', 'role' => User::ROLE_SUPERVISOR]);
        $this->assertDatabaseHas('companies', ['name' => 'New Tech Corp']);

        $company = Company::where('name', 'New Tech Corp')->first();
        $this->assertDatabaseHas('supervisor_profiles', [
            'position_title' => 'Project Manager',
            'company_id' => $company->id,
        ]);
    }

    public function test_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->coordinator)->post(route('coordinator.supervisors.store'), [
            'create_company' => '1',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'phone', 'company_name', 'company_industry']);
    }
}
