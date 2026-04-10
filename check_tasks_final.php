<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Assignment;
use App\Models\Task;

$assignment = Assignment::find(6);
if ($assignment) {
    echo "Assignment 6 (messy): " . $assignment->student->name . PHP_EOL;
    $tasks = $assignment->tasks;
    echo "Total tasks: " . $tasks->count() . PHP_EOL;
    $tasks->each(function($t) {
        echo "  - ID: " . $t->id . ", Title: " . $t->title . ", Semester: " . ($t->semester ?? 'NULL') . ", Status: " . $t->status . PHP_EOL;
    });
}
