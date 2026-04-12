#!/usr/bin/env pwsh

# Get the pending approvals page to extract CSRF token
Write-Host "Getting pending approvals page..."
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/admin/users/pending" -UseBasicParsing
$cookies = $response.BaseCookies

# Extract CSRF token
if ($response.Content -match '<meta name="csrf-token" content="([^"]+)"') {
    $csrfToken = $matches[1]
    Write-Host "✓ Found CSRF token: $csrfToken"
} else {
    Write-Host "❌ Could not find CSRF token"
    exit
}

# Find a pending user ID from the response
if ($response.Content -match 'user-(\d+)') {
    $userId = $matches[1]
    Write-Host "✓ Found pending user ID: $userId"
} else {
    Write-Host "⚠️  Could not find user ID in page - using ID 20"
    $userId = 20
}

# Test the reject endpoint
Write-Host "`nTesting rejection for user $userId..."
try {
    $rejectBody = @{
        '_token' = $csrfToken
    }
    
    $rejectResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/admin/users/$userId/reject" `
        -Method POST `
        -Body $rejectBody `
        -Headers @{
            'X-CSRF-TOKEN' = $csrfToken
        } `
        -WebSession $cookies `
        -UseBasicParsing `
        -SkipHttpErrorCheck
    
    Write-Host "Status Code: $($rejectResponse.StatusCode)"
    
    if ($rejectResponse.StatusCode -eq 302) {
        Write-Host "✓ Redirect response received (302)"
        Write-Host "Location: $($rejectResponse.Headers.Location)"
    } elseif ($rejectResponse.StatusCode -eq 200) {
        Write-Host "Response received (200)"
        # Check if there's an error message in the response
        if ($rejectResponse.Content -match 'An error occurred') {
            Write-Host "❌ Found error message in response"
            $errorMatch = $rejectResponse.Content -match '<div[^>]*class="[^"]*error[^"]*"[^>]*>([^<]+)'
            if ($LASTINDEX -gt 0) {
                Write-Host "Error: $($matches[1])"
            }
        } else {
            Write-Host "✓ Page loaded, no obvious errors"
        }
    }
    
}
catch {
    Write-Host "❌ Request failed: $_"
    Write-Host "Error Type: $($_.Exception.GetType().Name)"
}

Write-Host "`nDone"
