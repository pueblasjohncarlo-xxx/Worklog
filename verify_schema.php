<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking audit_logs table ===\n";
if (Schema::hasTable('audit_logs')) {
    echo "✓ audit_logs table exists\n";
    $columns = Schema::getColumnListing('audit_logs');
    echo "  Columns: " . implode(', ', $columns) . "\n";
} else {
    echo "❌ audit_logs table DOES NOT EXIST\n";
}

echo "\n=== Checking users table crucial columns ===\n";
$criticalCols = [
    'status', 'is_approved', 'has_requested_account', 
    'approved_at', 'rejected_at', 'approved_by'
];

foreach ($criticalCols as $col) {
    if (Schema::hasColumn('users', $col)) {
        echo "✓ {$col} exists\n";
    } else {
        echo "❌ {$col} MISSING\n";
    }
}

echo "\n=== Testing direct rejection update ===\n";
$user = \App\Models\User::find(19);
if ($user) {
    echo "Found user ID 19\n";
    echo "  Before: status={$user->status}, has_requested_account={$user->has_requested_account}\n";
    
    // Reset the user first
    $user->update([
        'status' => 'pending',
        'has_requested_account' => true,
        'is_approved' => false,
    ]);
    echo "  Reset to pending\n";
    
    // Try the rejection
    try {
        $user->update([
            'is_approved' => false,
            'status' => 'rejected',
            'has_requested_account' => false,
            'rejected_at' => now(),
        ]);
        $user->refresh();
        echo "✓ Update successful\n";
        echo "  After: status={$user->status}, has_requested_account={$user->has_requested_account}\n";
    } catch (\Exception $e) {
        echo "❌ Update failed: " . $e->getMessage() . "\n";
    }
}
?>
