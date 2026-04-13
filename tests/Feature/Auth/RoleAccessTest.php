<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::create([
            'name' => 'Student',
            'email' => 'student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $response = $this->actingAs($student)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
    }

    public function test_registration_creates_student_role_by_default(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'section' => 'BSIT-4A',
            'department' => 'Computer Technology',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'email' => 'student@example.com',
            'role' => User::ROLE_STUDENT,
            'section' => 'BSIT-4A',
            'department' => 'Computer Technology',
            'status' => 'pending',
        ]);
    }

    public function test_registration_rejects_invalid_student_section_or_major(): void
    {
        $response = $this->post('/register', [
            'name' => 'Invalid Student',
            'email' => 'invalid.student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => User::ROLE_STUDENT,
            'section' => 'BSIT-5X',
            'department' => 'Random Major',
        ]);

        $response->assertSessionHasErrors(['section', 'department']);

        $this->assertDatabaseMissing('users', [
            'email' => 'invalid.student@example.com',
        ]);
    }

    public function test_public_registration_cannot_use_coordinator_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Invalid Coordinator',
            'email' => 'coord@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => User::ROLE_COORDINATOR,
        ]);

        $response->assertSessionHasErrors(['role']);

        $this->assertDatabaseMissing('users', [
            'email' => 'coord@example.com',
        ]);
    }
}
