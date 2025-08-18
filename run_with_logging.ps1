# PowerShell script to run katana_to_woo.py with logging
param(
    [string]$LogFile = "",
    [switch]$DryRun = $true,
    [int]$IntegrationId = 0,
    [switch]$Verbose = $false
)

# Generate timestamp for log file if not provided
if ($LogFile -eq "") {
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $LogFile = "sync_log_$timestamp.txt"
}

# Build the command
$cmd = "python scripts/katana_to_woo.py"
if ($DryRun) { $cmd += " --dry-run" }
if ($IntegrationId -gt 0) { $cmd += " --integration-id $IntegrationId" }
if ($Verbose) { $cmd += " --verbose" }

# Log header
$header = @"
===========================================
PimForce Sync Log
Date: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
Command: $cmd
Log File: $LogFile
===========================================

"@

$header | Out-File -FilePath $LogFile -Encoding UTF8

# Run the command and capture output
Write-Host "Running: $cmd"
Write-Host "Logging to: $LogFile"
Write-Host ""

# Execute command and redirect all output to file
Invoke-Expression "$cmd 2>&1" | Tee-Object -FilePath $LogFile -Append

# Add footer
$footer = @"

===========================================
Sync completed at: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
===========================================
"@

$footer | Out-File -FilePath $LogFile -Append -Encoding UTF8

Write-Host ""
Write-Host "Log saved to: $LogFile"
