<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_show_handles_invalid_encrypted_password_without_crashing(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $user = User::create([
            'name' => 'Broken Password User',
            'email' => 'broken@example.test',
            'password' => bcrypt('password'),
            'encrypted_password' => 'not-a-valid-payload',
            'role' => User::ROLE_STUDENT,
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.show', $user));

        $response->assertOk();
        $response->assertSee('Stored password cannot be decrypted');
        $response->assertSee('Reset the password to restore visibility');
    }
}
