param(
    [switch]$WithMariaDb,
    [switch]$SkipBrowser
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot

function Invoke-ProjectStep {
    param([string]$Directory, [string]$Command, [string[]]$Arguments)

    Push-Location (Join-Path $root $Directory)
    try {
        & $Command @Arguments
        if ($LASTEXITCODE -ne 0) {
            throw "$Command $($Arguments -join ' ') failed with exit code $LASTEXITCODE"
        }
    }
    finally {
        Pop-Location
    }
}

Invoke-ProjectStep 'backend' 'composer' @('validate', '--strict', '--no-check-publish')
Invoke-ProjectStep 'backend' 'composer' @('audit', '--no-interaction')
Invoke-ProjectStep 'backend' 'php' @('vendor/bin/pint', '--test')
Invoke-ProjectStep 'backend' 'php' @('vendor/bin/phpstan', 'analyse', '--memory-limit=512M', '--no-progress')
Invoke-ProjectStep 'backend' 'php' @('artisan', 'test')
if ($WithMariaDb) {
    Invoke-ProjectStep 'backend' 'php' @('vendor/bin/phpunit', '--configuration', 'phpunit.mysql.xml')
}
Invoke-ProjectStep 'backend' 'npm' @('run', 'lint')
Invoke-ProjectStep 'backend' 'npm' @('run', 'typecheck')
Invoke-ProjectStep 'backend' 'npm' @('run', 'test:ui')
Invoke-ProjectStep 'backend' 'npm' @('run', 'build')
Invoke-ProjectStep 'customer-portal' 'npm' @('run', 'lint')
Invoke-ProjectStep 'customer-portal' 'npm' @('run', 'typecheck')
Invoke-ProjectStep 'customer-portal' 'npm' @('test')
Invoke-ProjectStep 'customer-portal' 'npm' @('run', 'build')
if (-not $SkipBrowser) {
    Invoke-ProjectStep 'customer-portal' 'npm' @('run', 'test:e2e')
}

$expectedHashes = @{
    'Data/Template RFID 2025.xlsx' = 'bc980355e78422889f535121778c8311168d1dec6b617504fce4be4b8021775b'
    'Data/UPDATE KARTU RFID.xlsx' = 'eb31f93e9ca738d6364a257de3b244506d64db77e2a4cadc188e47f832a59e3c'
    'Data/template pengajuan.jpeg' = '18a51a8262f0df9c6df1476c5ac585a193cf426bd84caaf3479f7217deb60ada'
}

foreach ($entry in $expectedHashes.GetEnumerator()) {
    $actual = (Get-FileHash -Algorithm SHA256 -LiteralPath (Join-Path $root $entry.Key)).Hash.ToLowerInvariant()
    if ($actual -ne $entry.Value) {
        throw "Immutable source changed: $($entry.Key)"
    }
}

Write-Host 'Verification passed.' -ForegroundColor Green
