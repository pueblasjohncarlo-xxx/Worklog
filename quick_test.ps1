# Get the pending approvals page to extract CSRF token
Write-Host "Getting pending approvals page..."
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/admin/users/pending" -UseBasicParsing
$cookies = $response.BaseCookies

# Extract CSRF token
if ($response.Content -match '<meta name="csrf-token" content="([^"]+)"') {
    $csrfToken = $matches[1]
    Write-Host "Found CSRF token: $csrfToken"
} else {
    Write-Host "Could not find CSRF token"
    exit
}

# Use a known pending user ID
$userId = 20

Write-Host "Testing rejection for user $userId..."

$rejectBody = @{
    '_token' = $csrfToken
}

$rejectResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/admin/users/$userId/reject" -Method POST -Body $rejectBody -Headers @{'X-CSRF-TOKEN' = $csrfToken} -WebSession $cookies -UseBasicParsing

Write-Host "Status Code: $($rejectResponse.StatusCode)"

if ($rejectResponse.StatusCode -eq 302) {
    Write-Host "Redirect response received"
} else {
    Write-Host "Response type: $($rejectResponse.StatusCode)"
    if ($rejectResponse.Content.Length -gt 0) {
        Write-Host "Response length: $($rejectResponse.Content.Length) bytes"
    }
}

Write-Host "Done"
