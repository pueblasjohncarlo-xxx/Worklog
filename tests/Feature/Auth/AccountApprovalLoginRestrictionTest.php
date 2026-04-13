<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountApprovalLoginRestrictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_account_cannot_log_in(): void
    {
        User::create([
            'name' => 'Pending User',
            'email' => 'pending@example.test',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_STUDENT,
            'status' => 'pending',
            'is_approved' => false,
            'has_requested_account' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@example.test',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Your account is pending coordinator approval.',
        ]);

        $this->assertGuest();
    }

    public function test_rejected_account_cannot_log_in(): void
    {
        User::create([
            'name' => 'Rejected User',
            'email' => 'rejected@example.test',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_SUPERVISOR,
            'status' => 'rejected',
            'is_approved' => false,
            'has_requested_account' => false,
            'rejected_at' => now(),
            'rejection_reason' => 'Profile incomplete',
        ]);

        $response = $this->post('/login', [
            'email' => 'rejected@example.test',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Your account has been rejected. Please contact the coordinator.',
        ]);

        $this->assertGuest();
    }

    public function test_approved_account_can_log_in(): void
    {
        User::create([
            'name' => 'Approved User',
            'email' => 'approved@example.test',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_STUDENT,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'approved@example.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
