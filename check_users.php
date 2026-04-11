<?php
// Database check script
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::count();
$byRole = \App\Models\User::selectRaw('role, COUNT(*) as count')->groupBy('role')->get();

echo "=== USER DATABASE CHECK ===\n";
echo "Total Users: $users\n\n";
echo "Users by Role:\n";
foreach ($byRole as $row) {
    echo "  {$row->role}: {$row->count}\n";
}

if ($users > 0) {
    echo "\nFirst 5 users:\n";
    \App\Models\User::limit(5)->get()->each(function($user) {
        echo "  - {$user->name} ({$user->email}) - Role: {$user->role}\n";
    });
}
