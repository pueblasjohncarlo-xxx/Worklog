<?php
require_once 'bootstrap/app.php';

$app = app();
$schema = \Illuminate\Support\Facades\Schema::class;

echo "=== Current leaves table columns ===\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('leaves');
foreach ($columns as $col) {
    echo "- $col\n";
}

echo "\n=== Current assignments table columns ===\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('assignments');
foreach ($columns as $col) {
    echo "- $col\n";
}

echo "\nChecking specific columns:\n";
echo "- leaves.number_of_days exists: " . (\Illuminate\Support\Facades\Schema::hasColumn('leaves', 'number_of_days') ? 'YES' : 'NO') . "\n";
echo "- leaves.days_remaining exists: " . (\Illuminate\Support\Facades\Schema::hasColumn('leaves', 'days_remaining') ? 'YES' : 'NO') . "\n";
echo "- leaves.approval_timeline exists: " . (\Illuminate\Support\Facades\Schema::hasColumn('leaves', 'approval_timeline') ? 'YES' : 'NO') . "\n";
echo "- assignments.annual_leave_limit exists: " . (\Illuminate\Support\Facades\Schema::hasColumn('assignments', 'annual_leave_limit') ? 'YES' : 'NO') . "\n";
echo "- assignments.sick_leave_limit exists: " . (\Illuminate\Support\Facades\Schema::hasColumn('assignments', 'sick_leave_limit') ? 'YES' : 'NO') . "\n";
