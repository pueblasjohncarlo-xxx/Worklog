<?php

use App\Models\User;
use App\Models\Assignment;
use App\Models\Task;

// Find the student "messy"
$student = User::where('name', 'messy')->orWhere('name', 'Messy')->first();

if (!$student) {
    die("Student 'messy' not found\n");
}

echo "=== STUDENT INFO ===\n";
echo "ID: " . $student->id . "\n";
echo "Name: " . $student->name . "\n";
echo "Role: " . $student->role . "\n\n";

// Check assignments
$assignments = Assignment::where('student_id', $student->id)->get();
echo "=== ASSIGNMENTS FOR THIS STUDENT ===\n";
echo "Total: " . $assignments->count() . "\n";

foreach ($assignments as $a) {
    echo "\nAssignment ID: " . $a->id . "\n";
    echo "Status: " . $a->status . "\n";
    echo "Supervisor: " . ($a->supervisor ? $a->supervisor->name : "NULL") . "\n";
    
    // Check tasks for this assignment
    $tasks = Task::where('assignment_id', $a->id)->get();
    echo "Tasks in this assignment: " . $tasks->count() . "\n";
    
    foreach ($tasks as $t) {
        echo "  - {$t->title} (Semester: {$t->semester}, Status: {$t->status})\n";
    }
}

if ($assignments->isEmpty()) {
    echo "No assignments found for this student!\n";
}
