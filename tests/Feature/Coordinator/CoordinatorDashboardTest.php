<?php

namespace Tests\Feature\Coordinator;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoordinatorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_dashboard_renders_with_custom_layout()
    {
        $coordinator = User::create([
            'name' => 'Coordinator User',
            'email' => 'coord@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
        ]);

        $response = $this->actingAs($coordinator)->get(route('coordinator.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('WorkLog');
        $response->assertSee('Student Overview');
        $response->assertSee('Coordinator dashboard');
    }
}
