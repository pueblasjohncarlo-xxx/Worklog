<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tasks = \App\Models\Task::where('assignment_id', 6)->orderBy('id')->get();

echo "=== Tasks for Student 'messy' (Assignment 6) ===\n\n";
echo "Total tasks: " . count($tasks) . "\n\n";

foreach ($tasks as $task) {
    echo "ID: {$task->id}\n";
    echo "  Title: {$task->title}\n";
    echo "  Semester: " . ($task->semester ? $task->semester : 'NULL') . "\n";
    echo "  Status: {$task->status}\n";
    echo "  Created: {$task->created_at}\n";
    echo "\n";
}

// Test the controller logic
echo "=== Testing Controller Logic ===\n\n";

$student = \App\Models\User::find(\App\Models\Assignment::find(6)->student_id);
$assignment = \App\Models\Assignment::where('student_id', $student->id)->first();

$allTasks = \App\Models\Task::where('assignment_id', $assignment->id)
    ->orderBy('created_at', 'desc')
    ->get()
    ->map(function ($task) {
        if (empty($task->semester)) {
            $task->semester = '1st';
        }
        return $task;
    });

echo "After filtering logic:\n";
$sem1 = $allTasks->where('semester', '1st');
$sem2 = $allTasks->where('semester', '2nd');

echo "1st Semester tasks: " . count($sem1) . "\n";
echo "2nd Semester tasks: " . count($sem2) . "\n";

foreach ($sem1 as $t) {
    echo "  1st: {$t->title} (semester={$t->semester})\n";
}
foreach ($sem2 as $t) {
    echo "  2nd: {$t->title} (semester={$t->semester})\n";
}
