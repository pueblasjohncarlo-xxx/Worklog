<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Assignment;
use App\Models\Task;
use App\Models\Company;
use Illuminate\Console\Command;

class FixTaskSystem extends Command
{
    protected $signature = 'fix:task-system';
    protected $description = 'Fix the task system by verifying and creating test data';

    public function handle()
    {
        $this->info('=== TASK SYSTEM FIX ===\n');

        // Step 1: Check if assignments exist
        $this->info('Step 1: Checking assignments...');
        $assignments = Assignment::all();
        $this->info("Total assignments: {$assignments->count()}");

        if ($assignments->isEmpty()) {
            $this->error("❌ No assignments found! Creating test data...\n");
            $this->createTestData();
        } else {
            $this->info("✓ Assignments exist:\n");
            foreach ($assignments as $a) {
                $studentName = $a->student ? $a->student->name : 'NULL';
                $supervisorName = $a->supervisor ? $a->supervisor->name : 'NULL';
                $this->line("  ID: {$a->id} | Student: {$studentName} | Supervisor: {$supervisorName}");
            }
        }

        // Step 2: Check tasks
        $this->info('\nStep 2: Checking tasks...');
        $tasks = Task::all();
        $this->info("Total tasks: {$tasks->count()}");

        foreach (Assignment::all() as $assignment) {
            $count = Task::where('assignment_id', $assignment->id)->count();
            $student = $assignment->student ? $assignment->student->name : 'Unknown';
            $this->line("  Assignment {$assignment->id} ({$student}): {$count} tasks");
        }

        // Step 3: Create test task if needed
        $this->info('\nStep 3: Creating test task...');
        
        $student = User::where('role', 'student')->first();
        $supervisor = User::where('role', 'supervisor')->first();

        if (!$student || !$supervisor) {
            $this->error('❌ No student or supervisor users found!');
            return;
        }

        // Check if assignment exists between them
        $assignment = Assignment::where('student_id', $student->id)
            ->where('supervisor_id', $supervisor->id)
            ->first();

        if (!$assignment) {
            $this->warn("Creating assignment between {$student->name} and {$supervisor->name}...");
            $company = Company::first();
            if (!$company) {
                $this->error('No company found!');
                return;
            }

            $assignment = Assignment::create([
                'student_id' => $student->id,
                'supervisor_id' => $supervisor->id,
                'coordinator_id' => User::where('role', '=', 'coordinator')->first()?->id,
                'company_id' => $company->id,
                'status' => 'active',
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(4),
                'required_hours' => 240,
            ]);
            $this->info("✓ Created assignment ID: {$assignment->id}");
        }

        // Create test tasks
        $this->warn('Creating test tasks...');
        for ($i = 1; $i <= 3; $i++) {
            $task = Task::create([
                'assignment_id' => $assignment->id,
                'title' => "Test Task #{$i}",
                'description' => "This is test task number {$i}",
                'semester' => ($i % 2 === 0) ? '2nd' : '1st',
                'status' => 'pending',
                'due_date' => now()->addDays($i * 3),
            ]);
            $this->info("✓ Created task ID: {$task->id} (Semester: {$task->semester})");
        }

        $this->info('\n=== SUMMARY ===');
        $this->info("✓ System is ready!");
        $this->info("\nTest with:");
        $this->info("• Student: {$student->name}");
        $this->info("• Supervisor: {$supervisor->name}");
        $this->info("• Assignment ID: {$assignment->id}");
        $this->info("\nGo to: http://127.0.0.1:8000/student/tasks");
    }

    private function createTestData()
    {
        $this->info('Creating test data structure...');

        // Create company if needed
        $company = Company::first();
        if (!$company) {
            $company = Company::create([
                'name' => 'Test Company',
                'industry' => 'Technology',
            ]);
            $this->info("✓ Created company: {$company->name}");
        }

        // Create users if needed
        $student = User::where('role', 'student')->first();
        if (!$student) {
            $student = User::create([
                'name' => 'Test Student',
                'email' => 'student@test.com',
                'password' => bcrypt('password'),
                'role' => 'student',
            ]);
            $this->info("✓ Created student: {$student->name}");
        }

        $supervisor = User::where('role', 'supervisor')->first();
        if (!$supervisor) {
            $supervisor = User::create([
                'name' => 'Test Supervisor',
                'email' => 'supervisor@test.com',
                'password' => bcrypt('password'),
                'role' => 'supervisor',
            ]);
            $this->info("✓ Created supervisor: {$supervisor->name}");
        }

        // Create assignment
        $assignment = Assignment::create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'coordinator_id' => User::where('role', 'coordinator')->first()?->id,
            'company_id' => $company->id,
            'status' => 'active',
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addMonths(4),
            'required_hours' => 240,
        ]);
        $this->info("✓ Created assignment: {$assignment->id}");
    }
}
