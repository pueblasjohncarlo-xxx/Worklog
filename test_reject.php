<?php
/**
 * Test script to directly test the reject functionality
 */

// Clear the old log
$logPath = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    unlink($logPath);
}

// Get the database and check user status first
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

// Find a pending user to test with
$pendingUser = User::where('status', 'pending')->where('has_requested_account', true)->first();

if (!$pendingUser) {
    echo "❌ No pending users found. Creating a test user...\n";
    $testUser = User::create([
        'name' => 'Test Pending User',
        'email' => 'test.pending.' . time() . '@example.com',
        'password' => bcrypt('password'),
        'status' => 'pending',
        'has_requested_account' => true,
    ]);
    $pendingUser = $testUser;
    echo "✓ Created test user ID: {$pendingUser->id}\n";
}

echo "📋 User Details Before Rejection:\n";
echo "  ID: {$pendingUser->id}\n";
echo "  Email: {$pendingUser->email}\n";
echo "  Status: {$pendingUser->status}\n";
echo "  Has Requested Account: " . ($pendingUser->has_requested_account ? 'Yes' : 'No') . "\n";
echo "  Is Approved: " . (is_null($pendingUser->is_approved) ? 'NULL' : ($pendingUser->is_approved ? 'Yes' : 'No')) . "\n\n";

// Check what columns exist in users table
echo "📊 User Table Columns (approval-related):\n";
$columns = DB::select('DESCRIBE users');
foreach ($columns as $col) {
    if (in_array($col->Field, ['id', 'status', 'is_approved', 'has_requested_account', 'approved_at', 'rejected_at', 'approved_by'])) {
        echo "  {$col->Field}: {$col->Type} (Null: {$col->Null}, Default: {$col->Default})\n";
    }
}

echo "\n🔧 Attempting rejection update...\n";

try {
    $pendingUser->update([
        'is_approved' => false,
        'status' => 'rejected',
        'has_requested_account' => false,
        'rejected_at' => now(),
    ]);
    echo "✓ Update successful!\n";
    
    // Refresh and check
    $pendingUser->refresh();
    echo "📋 User Details After Rejection:\n";
    echo "  Status: {$pendingUser->status}\n";
    echo "  Has Requested Account: " . ($pendingUser->has_requested_account ? 'Yes' : 'No') . "\n";
    echo "  Is Approved: " . (is_null($pendingUser->is_approved) ? 'NULL' : ($pendingUser->is_approved ? 'Yes' : 'No')) . "\n";
    echo "  Rejected At: " . ($pendingUser->rejected_at ? $pendingUser->rejected_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
} catch (\Exception $e) {
    echo "❌ Update failed!\n";
    echo "  Error: " . $e->getMessage() . "\n";
    echo "  Class: " . get_class($e) . "\n";
    echo "\n📋 Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
?>
