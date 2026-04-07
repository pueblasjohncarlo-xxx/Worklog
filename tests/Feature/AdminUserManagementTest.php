<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_admin_can_view_user_management_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee($this->admin->name);
    }

    public function test_admin_can_create_new_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.test',
            'password' => 'password123',
            'role' => User::ROLE_STUDENT,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.test',
            'role' => User::ROLE_STUDENT,
        ]);
    }

    public function test_admin_can_update_user_role(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.users.update-role', $user), [
            'role' => User::ROLE_COORDINATOR,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertEquals(User::ROLE_COORDINATOR, $user->fresh()->role);
    }

    public function test_admin_can_delete_user(): void
    {
        $user = User::create([
            'name' => 'To Be Deleted',
            'email' => 'delete@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $student = User::create([
            'name' => 'Student',
            'email' => 'student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $this->actingAs($student)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($student)->post(route('admin.users.store'))->assertForbidden();
        $this->actingAs($student)->delete(route('admin.users.destroy', $this->admin))->assertForbidden();
    }
}
