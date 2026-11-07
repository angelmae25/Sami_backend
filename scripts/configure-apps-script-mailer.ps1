[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$backendRoot = Split-Path -Parent $PSScriptRoot
$envPath = Join-Path $backendRoot '.env'
if (-not (Test-Path -LiteralPath $envPath)) {
    throw "Backend .env was not found at $envPath. Copy .env.example to .env first."
}

$url = (Read-Host 'Deployed Google Apps Script web app URL (the URL ending in /exec)').Trim()
if ($url -notmatch '^https://script\.google\.com/macros/s/[^/]+/exec$') {
    throw 'Enter the deployed Google Apps Script URL that ends in /exec.'
}

$bytes = [byte[]]::new(32)
$rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
try { $rng.GetBytes($bytes) } finally { $rng.Dispose() }
$token = [Convert]::ToBase64String($bytes).TrimEnd('=').Replace('+', '-').Replace('/', '_')

$updates = [ordered]@{
    SAMI_OTP_MAILER = 'apps_script'
    SAMI_APPS_SCRIPT_URL = $url
    SAMI_APPS_SCRIPT_TOKEN = $token
}
$lines = [System.Collections.Generic.List[string]]::new()
$lines.AddRange([string[]](Get-Content -LiteralPath $envPath))
for ($i = $lines.Count - 1; $i -ge 0; $i--) {
    if ($lines[$i] -match '^\s*#?\s*MAIL_URL=') { $lines.RemoveAt($i) }
}
foreach ($key in $updates.Keys) {
    $found = $false
    for ($i = 0; $i -lt $lines.Count; $i++) {
        if ($lines[$i] -match "^\s*#?\s*$([regex]::Escape($key))=") {
            $lines[$i] = "$key=$($updates[$key])"
            $found = $true
        }
    }
    if (-not $found) { $lines.Add("$key=$($updates[$key])") }
}
[System.IO.File]::WriteAllLines($envPath, $lines, [System.Text.UTF8Encoding]::new($false))
$bytes = $null

Push-Location $backendRoot
try {
    & php artisan config:clear
    if ($LASTEXITCODE -ne 0) { throw 'Laravel could not clear its cached configuration.' }
} finally {
    Pop-Location
}

Write-Host ''
Write-Host 'Copy this token into Apps Script > Project Settings > Script properties.'
Write-Host 'Property name: SAMI_OTP_TOKEN'
Write-Host "Property value: $token"
Write-Host 'Keep the token private. Do not put it in the Flutter app or share it in chat.'
[void](Read-Host 'After saving the property, press Enter here to verify the deployment')

try {
    $payload = @{ action = 'health'; token = $token } | ConvertTo-Json -Compress
    $result = Invoke-RestMethod -Uri $url -Method Post -ContentType 'application/json' -Body $payload -TimeoutSec 30
    if ($result.ok -ne $true) { throw 'The Apps Script did not confirm this token.' }
} catch {
    throw 'Apps Script verification failed. Confirm the web app is deployed as you, accessible to anyone, and the Script property matches the token above.'
}
Write-Host 'Google Apps Script is connected. Restart the Laravel API, then request an OTP in SAMI.'
