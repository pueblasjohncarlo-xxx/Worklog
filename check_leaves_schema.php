<?php
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Schema;

echo "=== LEAVES TABLE SCHEMA ===\n\n";

$columns = Schema::getColumnListing('leaves');
echo "Columns in leaves table:\n";
foreach ($columns as $column) {
    echo "  - $column\n";
}

echo "\nChecking required columns:\n";
echo "  number_of_days: " . (Schema::hasColumn('leaves', 'number_of_days') ? "✓" : "✗") . "\n";
echo "  attachment_path: " . (Schema::hasColumn('leaves', 'attachment_path') ? "✓" : "✗") . "\n";
echo "  submitted_at: " . (Schema::hasColumn('leaves', 'submitted_at') ? "✓" : "✗") . "\n";
echo "  reviewer_remarks: " . (Schema::hasColumn('leaves', 'reviewer_remarks') ? "✓" : "✗") . "\n";
echo "  reviewer_id: " . (Schema::hasColumn('leaves', 'reviewer_id') ? "✓" : "✗") . "\n";
echo "  reviewed_at: " . (Schema::hasColumn('leaves', 'reviewed_at') ? "✓" : "✗") . "\n";
echo "  signature_path: " . (Schema::hasColumn('leaves', 'signature_path') ? "✓" : "✗") . "\n";
