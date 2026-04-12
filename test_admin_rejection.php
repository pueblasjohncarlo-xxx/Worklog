<?php
// Test with actual login

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Find or create an admin user
$admin = User::where('role', 'admin')->first();

if (!$admin) {
    echo "Creating test admin user...\n";
    $admin = User::create([
        'name' => 'Test Admin',
        'email' => 'admin@test.local',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'status' => 'approved',
    ]);
}

echo "Admin user: {$admin->email} (ID: {$admin->id})\n";

// Log in as admin
Auth::login($admin);
echo "Logged in as admin\n";

// Now try the rejection directly
$pendingUser = User::where('status', 'pending')->where('has_requested_account', true)->first();

if (!$pendingUser) {
    $pendingUser = User::create([
        'name' => 'Test Pending',
        'email' => 'pending.' . time() . '@test.local',
        'password' => bcrypt('password'),
        'role' => 'student',
        'status' => 'pending',
        'has_requested_account' => true,
    ]);
    echo "Created pending user: {$pendingUser->email}\n";
} else {
    echo "Using pending user: {$pendingUser->email} (ID: {$pendingUser->id})\n";
}

echo "\nTesting rejection...\n";

// Try direct rejection
try {
    $pendingUser->update([
        'is_approved' => false,
        'status' => 'rejected',
        'has_requested_account' => false,
        'rejected_at' => now(),
    ]);
    
    // Calculate and log audit
    $logData = [
        'rejected_by_admin' => $admin->name,
        'ip_address' => '127.0.0.1',
    ];
    
    \App\Models\AuditLog::create([
        'user_id' => $admin->id,
        'action' => 'admin_user_rejected',
        'auditable_type' => User::class,
        'auditable_id' => $pendingUser->id,
        'new_values' => json_encode($logData),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'CLI Test',
    ]);
    
    echo "✓ Rejection successful!\n";
    $pendingUser->refresh();
    echo "  Status: {$pendingUser->status}\n";
    echo "  Has Requested: " . ($pendingUser->has_requested_account ? 'Yes' : 'No') . "\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
?>
