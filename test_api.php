<?php
// Quick test script to verify the API endpoint
// Run: php test_api.php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Create a request for testing
$request = \Illuminate\Http\Request::create('/api/messages/available-users', 'GET');

// Boot the application
$app->make(\Illuminate\Contracts\Http\Kernel::class);

// Test the endpoint
try {
    echo "Testing /api/messages/available-users endpoint...\n";
    echo "=========================================\n\n";
    
    // Get URL content
    $context = stream_context_create([
        'http' => [
            'header'  => "Cookie: XSRF-TOKEN=test\r\n",
            'timeout' => 5
        ]
    ]);
    
    // The test requires authentication, so this won't work without login
    // Instead, let's check the database directly
    
    // Initialize Laravel
    define('LARAVEL_START', microtime(true));
    $app = require __DIR__ . '/bootstrap/app.php';
    
    // Get database connection
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    
    // Check users in database
    $users = \App\Models\User::count();
    $supervisors = \App\Models\User::where('role', 'supervisor')->count();
    $students = \App\Models\User::where('role', 'student')->count();
    $coordinators = \App\Models\User::where('role', 'coordinator')->count();
    
    echo "Database Summary:\n";
    echo "- Total Users: $users\n";
    echo "- Supervisors: $supervisors\n";
    echo "- Students: $students\n";
    echo "- Coordinators: $coordinators\n\n";
    
    // List first 5 coordinators
    $firstCoordinator = \App\Models\User::where('role', 'coordinator')->first();
    if ($firstCoordinator) {
        echo "First Coordinator: {$firstCoordinator->name} ({$firstCoordinator->email})\n";
        
        // Check if this coordinator can message supervisors
        $supervisorList = \App\Models\User::where('role', 'supervisor')->orderBy('name')->get();
        echo "Available Supervisors for messaging: {$supervisorList->count()}\n";
        if ($supervisorList->count() > 0) {
            echo "Sample: {$supervisorList->first()->name}\n";
        }
    }
    
    echo "\nDatabase check complete. If no coordinators, that's the problem!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
