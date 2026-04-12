<?php
/**
 * Test the HTTP rejection endpoint
 */
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Find a pending user to test with
$pendingUser = User::where('status', 'pending')->where('has_requested_account', true)->first();

if (!$pendingUser) {
    $testUser = User::create([
        'name' => 'Test Pending User HTTP',
        'email' => 'test.pending.http.' . time() . '@example.com',
        'password' => bcrypt('password'),
        'status' => 'pending',
        'has_requested_account' => true,
    ]);
    $pendingUser = $testUser;
}

echo "Testing HTTP rejection for user ID: {$pendingUser->id}\n";
echo "Before: Status = {$pendingUser->status}, Has Requested = {$pendingUser->has_requested_account}\n";

// Make an HTTP request to the reject endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/admin/users/{$pendingUser->id}/reject");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');

// Add headers (including Laravel's CSRF token)
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: text/html',
));

// Get the page first to get CSRF token
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/admin/users/pending");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
$html = curl_exec($ch);

// Extract CSRF token
if (preg_match('/<meta name="csrf-token" content="([^"]+)/', $html, $matches)) {
    $csrfToken = $matches[1];
    echo "Found CSRF token: {$csrfToken}\n";
} else {
    echo "⚠️  No CSRF token found in page\n";
    die();
}

// Now attempt the rejection
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/admin/users/{$pendingUser->id}/reject");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'X-CSRF-TOKEN: ' . $csrfToken,
));
curl_setopt($ch, CURLOPT_POSTFIELDS, '_token=' . urlencode($csrfToken));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Response Code: {$httpCode}\n";

// Check if rejection was successful
$pendingUser->refresh();
echo "After: Status = {$pendingUser->status}, Has Requested = {$pendingUser->has_requested_account}\n";

if ($pendingUser->status === 'rejected' && !$pendingUser->has_requested_account) {
    echo "✓ Rejection successful!\n";
} else {
    echo "❌ Rejection failed - user status not updated\n";
    echo "Response preview: " . substr($response, 0, 500) . "\n";
}
?>
