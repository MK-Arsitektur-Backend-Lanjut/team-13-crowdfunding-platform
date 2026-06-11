# Postman Test Runner for Team 13 Crowdfunding API
# PowerShell Version
#
# Usage:
#   .\runner.ps1                    # Run with defaults
#   .\runner.ps1 -Delay 500         # Custom delay (ms)
#   .\runner.ps1 -Timeout 8000      # Custom timeout (ms)
#   .\runner.ps1 -StopOnError       # Stop on first error
#   .\runner.ps1 -Insecure          # Allow self-signed certificates

param(
    [int]$Delay = 100,
    [int]$Timeout = 5000,
    [switch]$StopOnError,
    [switch]$Insecure
)

# Set error action preference
$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host "Team 13 Crowdfunding API - Postman Test Runner" -ForegroundColor Cyan
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""

# Get script directory
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$Collection = Join-Path $ScriptDir "Team-13-Crowdfunding.postman_collection.json"
$Environment = Join-Path $ScriptDir "Team-13-Local.postman_environment.json"

# Check Node.js
Write-Host "Checking prerequisites..." -ForegroundColor Yellow
try {
    $NodeVersion = node --version 2>$null
    Write-Host "✓ Node.js found: $NodeVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Node.js is not installed or not in PATH" -ForegroundColor Red
    Write-Host "Please install Node.js from: https://nodejs.org/" -ForegroundColor Yellow
    exit 1
}

# Check Newman
try {
    $NewmanVersion = npm list -g newman 2>$null | grep newman | Select-Object -First 1
    if ($NewmanVersion) {
        Write-Host "✓ Newman is installed" -ForegroundColor Green
    }
} catch {
    Write-Host "Installing Newman..." -ForegroundColor Yellow
    npm install -g newman
    if ($LASTEXITCODE -ne 0) {
        Write-Host "✗ Failed to install Newman" -ForegroundColor Red
        exit 1
    }
    Write-Host "✓ Newman installed successfully" -ForegroundColor Green
}

# Check collection file
if (-not (Test-Path $Collection)) {
    Write-Host "✗ Collection file not found: $Collection" -ForegroundColor Red
    exit 1
}
Write-Host "✓ Collection file found" -ForegroundColor Green

# Check environment file
if (-not (Test-Path $Environment)) {
    Write-Host "✗ Environment file not found: $Environment" -ForegroundColor Red
    exit 1
}
Write-Host "✓ Environment file found" -ForegroundColor Green

Write-Host ""
Write-Host "Configuration:" -ForegroundColor Cyan
Write-Host "  Delay:    $Delay ms"
Write-Host "  Timeout:  $Timeout ms"
Write-Host "  Stop on error: $($StopOnError ? 'Yes' : 'No')"
Write-Host "  Insecure: $($Insecure ? 'Yes' : 'No')"
Write-Host ""
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""

# Build arguments
$Arguments = @(
    $Collection,
    "-e", $Environment,
    "--delay-request", $Delay,
    "--timeout", $Timeout,
    "-r", "cli,json",
    "--reporter-json-export", (Join-Path $ScriptDir "test-results.json")
)

if ($StopOnError) {
    $Arguments += "--bail"
}

if ($Insecure) {
    $Arguments += "--insecure"
}

# Run Newman
try {
    & newman run @Arguments
    $ExitCode = $LASTEXITCODE
} catch {
    Write-Host "Error running tests: $_" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "=====================================================" -ForegroundColor Cyan
if ($ExitCode -eq 0) {
    Write-Host "✓ All tests passed successfully!" -ForegroundColor Green
} else {
    Write-Host "⚠ Some tests failed. See output above for details." -ForegroundColor Yellow
}
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Detailed results: $(Join-Path $ScriptDir 'test-results.json')" -ForegroundColor Gray
Write-Host ""

exit $ExitCode
