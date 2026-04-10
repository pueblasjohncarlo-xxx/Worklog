<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class DebugStudent extends Command
{
    protected $signature = 'debug:student {student_id}';
    protected $description = 'Debug student tasks and assignments';

    public function handle()
    {
        $studentId = $this->argument('student_id');
        $student = User::find($studentId);

        if (!$student) {
            $this->error("Student not found with ID: $studentId");
            return;
        }

        $this->info("Student: {$student->name} (ID: {$student->id})");
        $this->info("Role: {$student->role}");

        $assignment = Assignment::where('student_id', $studentId)->first();
        if (!$assignment) {
            $this->error("No assignment found for this student");
            return;
        }

        $this->info("\n=== Assignment ===");
        $this->info("Assignment ID: {$assignment->id}");
        $this->info("Supervisor ID: {$assignment->supervisor_id}");
        $this->info("Status: {$assignment->status}");

        $tasks = Task::where('assignment_id', $assignment->id)->get();
        $this->info("\n=== Tasks ({$tasks->count()}) ===");

        if ($tasks->isEmpty()) {
            $this->warn("No tasks found for this assignment!");
            return;
        }

        foreach ($tasks as $task) {
            $this->line("ID: {$task->id} | Title: {$task->title}");
            $this->line("  Semester: {$task->semester} | Status: {$task->status}");
            $this->line("  Created: {$task->created_at}");
        }
    }
}
