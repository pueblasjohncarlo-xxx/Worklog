<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCompanyManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->supervisor = User::create([
            'name' => 'Supervisor User',
            'email' => 'supervisor@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
        ]);
    }

    public function test_admin_can_view_companies_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.companies.index'));
        $response->assertStatus(200);
        $response->assertSee('Company Management');
    }

    public function test_admin_can_create_company_with_associated_data()
    {
        $companyData = [
            'name' => 'Test Tech Solutions',
            'industry' => 'Technology',
            'type' => 'Private',
            'address' => '123 Tech Lane',
            'city' => 'Silicon Valley',
            'state' => 'California',
            'postal_code' => '94025',
            'country' => 'USA',
            'contact_person' => 'Jane Smith',
            'contact_email' => 'jane@testtech.com',
            'contact_phone' => '123-456-7890',
            'default_supervisor_id' => $this->supervisor->id,
            'work_opportunities' => ['Web Development', 'UI/UX Design'],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.companies.store'), $companyData);

        $response->assertRedirect(route('admin.companies.index'));
        $this->assertDatabaseHas('companies', [
            'name' => 'Test Tech Solutions',
            'default_supervisor_id' => $this->supervisor->id,
        ]);

        $company = Company::where('name', 'Test Tech Solutions')->first();
        $this->assertEquals(['Web Development', 'UI/UX Design'], $company->work_opportunities);
    }

    public function test_company_creation_requires_validation()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.companies.store'), []);

        $response->assertSessionHasErrors([
            'name', 'industry', 'type', 'address', 'city', 'state',
            'postal_code', 'country', 'contact_person', 'contact_email',
            'contact_phone', 'default_supervisor_id', 'work_opportunities',
        ]);
    }

    public function test_non_admin_cannot_access_company_management()
    {
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $this->actingAs($student)->get(route('admin.companies.index'))->assertForbidden();
        $this->actingAs($student)->post(route('admin.companies.store'), [])->assertForbidden();
    }

    public function test_admin_can_delete_company()
    {
        $company = Company::create([
            'name' => 'To Be Deleted',
            'industry' => 'Misc',
            'type' => 'Other',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => '123',
            'country' => 'Country',
            'contact_person' => 'Person',
            'contact_email' => 'email@test.com',
            'contact_phone' => '123',
            'default_supervisor_id' => $this->supervisor->id,
            'work_opportunities' => ['Work'],
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.companies.destroy', $company));

        $response->assertRedirect(route('admin.companies.index'));
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}
