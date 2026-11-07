$ErrorActionPreference = 'Stop'

$backendRoot = Split-Path -Parent $PSScriptRoot
$envPath = Join-Path $backendRoot '.env'
if (-not (Test-Path -LiteralPath $envPath)) {
    throw 'Laravel .env was not found. Run this from the SAMI backend project.'
}

$secureKey = Read-Host 'Gemini API key (input is hidden)' -AsSecureString
$pointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secureKey)
try {
    $apiKey = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($pointer).Trim()
    $apiKey = $apiKey.Trim('"').Trim("'") -replace '\s', ''
    if ($apiKey.Length -lt 20) {
        throw 'This value is too short to be a Gemini API key. Create/copy a Gemini API key from https://aistudio.google.com/app/apikey. Do not use your Gmail App Password. No configuration was changed.'
    }
    if ($apiKey -notmatch '^[A-Za-z0-9._-]+$') {
        throw 'The cleaned value contains characters that are not valid in a Gemini API key. Copy only the key from Google AI Studio. No configuration was changed.'
    }

    $lines = [System.Collections.Generic.List[string]]::new()
    $lines.AddRange([string[]](Get-Content -LiteralPath $envPath))
    $keyIndex = -1
    for ($index = 0; $index -lt $lines.Count; $index++) {
        if ($lines[$index] -match '^GEMINI_API_KEY=') {
            $keyIndex = $index
            break
        }
    }

    if ($keyIndex -ge 0) {
        $lines[$keyIndex] = "GEMINI_API_KEY=$apiKey"
    } else {
        $lines.Add("`nGEMINI_API_KEY=$apiKey")
    }

    Set-Content -LiteralPath $envPath -Value $lines -Encoding utf8
} finally {
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($pointer)
    $apiKey = $null
}

Push-Location $backendRoot
try {
    & php artisan config:clear
    if ($LASTEXITCODE -ne 0) {
        throw 'Could not clear Laravel configuration cache.'
    }
} finally {
    Pop-Location
}

Write-Host 'Gemini ID screening is configured. Restart the Laravel API before accepting ID uploads.'
