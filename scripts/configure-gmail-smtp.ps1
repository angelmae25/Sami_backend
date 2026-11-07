[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$backendRoot = Split-Path -Parent $PSScriptRoot
$envPath = Join-Path $backendRoot '.env'
if (-not (Test-Path -LiteralPath $envPath)) {
    throw "Backend .env was not found at $envPath. Copy .env.example to .env first."
}

$mailbox = (Read-Host 'Google mailbox (Gmail, Googlemail, or TUP Google Workspace)').Trim()
if ($mailbox -notmatch '^[^\s@]+@(?:gmail\.com|googlemail\.com|tup\.edu\.ph)$') {
    throw 'Enter a Gmail, Googlemail, or TUP Google Workspace address.'
}

$appPassword = $null
while ($null -eq $appPassword) {
    Write-Host 'Use a Google App Password, not your normal Google account password.'
    Write-Host 'It is 16 characters (Google may display it as four groups); spaces are okay.'
    $securePassword = Read-Host 'Google App Password (input is hidden)' -AsSecureString
    $ptr = [IntPtr]::Zero
    $candidate = $null
    try {
        $ptr = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
        $candidate = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($ptr).Replace(' ', '').Trim()
    } finally {
        if ($ptr -ne [IntPtr]::Zero) { [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($ptr) }
        $securePassword.Dispose()
    }
    if ($candidate -match '^[A-Za-z0-9]{16}$') {
        $appPassword = $candidate
    } else {
        $candidate = $null
        Write-Warning 'That entry is not 16 characters. Create an App Password at https://myaccount.google.com/apppasswords. Do not enter your regular account password.'
    }
}

# Values are written only to the ignored backend .env file. They are never printed.
$updates = [ordered]@{
    MAIL_MAILER = 'smtp'
    MAIL_SCHEME = 'smtp'
    MAIL_HOST = 'smtp.gmail.com'
    MAIL_PORT = '587'
    MAIL_USERNAME = $mailbox
    MAIL_PASSWORD = $appPassword
    MAIL_FROM_ADDRESS = $mailbox
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
$appPassword = $null
$updates['MAIL_PASSWORD'] = $null

Push-Location $backendRoot
try {
    & php artisan config:clear
    if ($LASTEXITCODE -ne 0) { throw 'Laravel could not clear its cached mail configuration.' }
    & php (Join-Path $PSScriptRoot 'verify-gmail-smtp.php')
    if ($LASTEXITCODE -ne 0) { throw 'Google did not authenticate this App Password. Generate a fresh App Password and run this script again. No email was sent.' }
} finally {
    Pop-Location
}
Write-Host 'Google SMTP authentication succeeded. Restart the Laravel API, then request a new OTP in SAMI.'
