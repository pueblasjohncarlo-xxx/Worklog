<?php

namespace Tests\Feature\Coordinator;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_registration_is_removed_from_pending_list(): void
    {
        $coordinator = User::create([
            'name' => 'Coordinator User',
            'email' => 'coordinator@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
            'email_verified_at' => now(),
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $pending = User::create([
            'name' => 'Pending Student',
            'email' => 'pending.student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
            'status' => 'pending',
            'is_approved' => false,
            'has_requested_account' => true,
        ]);

        $response = $this->actingAs($coordinator)
            ->post(route('coordinator.registrations.approve', $pending));

        $response->assertRedirect(route('coordinator.registrations.pending'));

        $this->assertDatabaseHas('users', [
            'id' => $pending->id,
            'status' => 'approved',
            'is_approved' => true,
        ]);

        $list = $this->actingAs($coordinator)
            ->get(route('coordinator.registrations.pending'));

        $list->assertOk();
        $list->assertDontSee('pending.student@example.test');
    }

    public function test_rejected_registration_is_removed_from_pending_list(): void
    {
        $coordinator = User::create([
            'name' => 'Coordinator User',
            'email' => 'coordinator2@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
            'email_verified_at' => now(),
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        $pending = User::create([
            'name' => 'Pending Supervisor',
            'email' => 'pending.supervisor@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPERVISOR,
            'status' => 'pending',
            'is_approved' => false,
            'has_requested_account' => true,
        ]);

        $response = $this->actingAs($coordinator)
            ->post(route('coordinator.registrations.reject', $pending), [
                'reason' => 'Incomplete profile details',
            ]);

        $response->assertRedirect(route('coordinator.registrations.pending'));

        $this->assertDatabaseHas('users', [
            'id' => $pending->id,
            'status' => 'rejected',
            'is_approved' => false,
            'has_requested_account' => false,
            'rejection_reason' => 'Incomplete profile details',
        ]);

        $list = $this->actingAs($coordinator)
            ->get(route('coordinator.registrations.pending'));

        $list->assertOk();
        $list->assertDontSee('pending.supervisor@example.test');
    }
}
