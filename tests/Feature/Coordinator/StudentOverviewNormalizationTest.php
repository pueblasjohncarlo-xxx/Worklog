<?php

namespace Tests\Feature\Coordinator;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentOverviewNormalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_student_overview_uses_normalized_labels_and_hides_rejected_students(): void
    {
        $coordinator = User::create([
            'name' => 'Coordinator',
            'email' => 'coordinator@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
        ]);

        User::create([
            'name' => 'Messy Student',
            'email' => 'messy.student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
            'status' => 'approved',
            'is_approved' => true,
            'has_requested_account' => true,
            'section' => '4A (COT)',
            'department' => 'COT',
        ]);

        User::create([
            'name' => 'Rejected Student',
            'email' => 'rejected.student@example.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
            'status' => 'rejected',
            'is_approved' => false,
            'has_requested_account' => false,
            'section' => 'BSIT-4B',
            'department' => 'Computer Technology',
        ]);

        $response = $this->actingAs($coordinator)->get(route('coordinator.student-overview'));

        $response->assertOk();
        $response->assertSeeText('BSIT-4A (Computer Technology)');
        $response->assertSeeText('Messy Student');
        $response->assertDontSeeText('Rejected Student');
        $response->assertDontSeeText('4A (COT)');
        $response->assertDontSeeText('NO SECTION');
    }
}
