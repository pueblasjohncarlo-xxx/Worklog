<?php
// Test the API endpoint directly
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a fake authenticated session
// Get a user to use as logged-in
$user = \App\Models\User::where('email', 'mark@gmail.com')->first();
if (!$user) {
    echo "ERROR: Test user not found\n";
    exit;
}

// Simulate authentication
\Illuminate\Support\Facades\Auth::login($user);

echo "=== SIMULATING REAL API CALL ===\n";
echo "Logged in as: {$user->name} (ID: {$user->id})\n\n";

echo "Test 1: Call apiAvailableUsers without search\n";
echo "-------------------------------------------\n";

// Create a controller instance
$controller = new \App\Http\Controllers\MessageController();

// Create a request object
$request = new \Illuminate\Http\Request();
$request->merge(['search' => '']);

// Call the method
$response = $controller->apiAvailableUsers($request);
$responseData = json_decode($response->getContent(), true);

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Success: " . ($responseData['success'] ? 'Yes' : 'No') . "\n";
echo "Total Users: " . $responseData['total'] . "\n";
echo "First 3 Users:\n";

if (!empty($responseData['users'])) {
    foreach (array_slice($responseData['users'], 0, 3) as $u) {
        echo "  - {$u['name']} ({$u['email']}) - {$u['role']}\n";
    }
} else {
    echo "  (empty)\n";
}

echo "\nTest 2: Call apiAvailableUsers with search 'sean'\n";
echo "-------------------------------------------\n";

$request = new \Illuminate\Http\Request();
$request->merge(['search' => 'sean']);

$response = $controller->apiAvailableUsers($request);
$responseData = json_decode($response->getContent(), true);

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Success: " . ($responseData['success'] ? 'Yes' : 'No') . "\n";
echo "Total Users: " . $responseData['total'] . "\n";
echo "Users:\n";

if (!empty($responseData['users'])) {
    foreach ($responseData['users'] as $u) {
        echo "  - {$u['name']} ({$u['email']}) - {$u['role']}\n";
    }
} else {
    echo "  (empty)\n";
}

echo "\n=== API TEST COMPLETE ===\n";
