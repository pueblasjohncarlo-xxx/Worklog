<?php
// Test the API endpoint
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate a logged-in user (Mark Roble - coordinator)
$currentUser = \App\Models\User::where('email', 'mark@gmail.com')->first();
if (!$currentUser) {
    echo "Current user not found\n";
    exit;
}

echo "=== TESTING apiAvailableUsers() ===\n";
echo "Logged in as: {$currentUser->name} ({$currentUser->role})\n";
echo "User ID: {$currentUser->id}\n\n";

// Get all users except current user
$allOthers = \App\Models\User::where('id', '!=', $currentUser->id)->get();
echo "Total Other Users: {$allOthers->count()}\n\n";

// Test without search
echo "--- Without Search ---\n";
$withoutSearch = \App\Models\User::where('id', '!=', $currentUser->id)->orderBy('name')->get();
echo "Count: {$withoutSearch->count()}\n";
echo "Users:\n";
$withoutSearch->each(function($user) {
    echo "  - {$user->name} ({$user->email}) - {$user->role}\n";
});

echo "\n--- With Search 'sean' ---\n";
$searchTerm = '%sean%';
$withSearch = \App\Models\User::where('id', '!=', $currentUser->id)
    ->where(function ($q) use ($searchTerm) {
        $q->where('name', 'LIKE', $searchTerm)
          ->orWhere('email', 'LIKE', $searchTerm);
    })
    ->orderBy('name')
    ->get();

echo "Count: {$withSearch->count()}\n";
echo "Users:\n";
$withSearch->each(function($user) {
    echo "  - {$user->name} ({$user->email}) - {$user->role}\n";
});
