<#
.SYNOPSIS
    Reports this machine's inventory to the hospital back-office.

.DESCRIPTION
    Collects hardware, OS, and installed-software details and posts them to
    /api/agent/computer-inventory, authenticating with the bearer token from
    config.json.

    Targets Windows PowerShell 5.1, which is what ships with Windows 10 and
    Server 2016 onward, so nothing needs installing on the fleet. That rules
    out ternaries, ?? , and -AsHashtable.

.PARAMETER ConfigPath
    Defaults to config.json beside this script. Copy config.example.json and
    fill in the endpoint and token.

.PARAMETER DryRun
    Collect and print the payload without sending it. Use this first.

.EXAMPLE
    .\report-inventory.ps1 -DryRun

.EXAMPLE
    Register a daily run as SYSTEM, jittered so a fleet does not arrive at once:

    $a = New-ScheduledTaskAction -Execute 'powershell.exe' `
        -Argument '-NoProfile -ExecutionPolicy Bypass -File "C:\ProgramData\HospitalAgent\report-inventory.ps1"'
    $t = New-ScheduledTaskTrigger -Daily -At 8am
    $t.RandomDelay = 'PT30M'
    Register-ScheduledTask -TaskName 'Hospital Inventory Agent' -Action $a -Trigger $t `
        -User 'SYSTEM' -RunLevel Highest
#>

[CmdletBinding()]
param(
    [string] $ConfigPath,
    [switch] $DryRun
)

Set-StrictMode -Version 2.0
$ErrorActionPreference = 'Stop'

# $PSScriptRoot is empty under 5.1 when the script is launched by a relative
# path, so the default cannot be set in the param block.
if (-not $ConfigPath) {
    $scriptDirectory = $PSScriptRoot

    if (-not $scriptDirectory -and $MyInvocation.MyCommand.Path) {
        $scriptDirectory = Split-Path -Parent $MyInvocation.MyCommand.Path
    }

    if (-not $scriptDirectory) {
        $scriptDirectory = (Get-Location).Path
    }

    $ConfigPath = Join-Path $scriptDirectory 'config.json'
}

$AgentVersion = '1.0.0'

# --------------------------------------------------------------------------
# Logging
# --------------------------------------------------------------------------

$script:LogPath = $null

function Write-Log {
    param(
        [Parameter(Mandatory)] [string] $Message,
        [ValidateSet('INFO', 'WARN', 'ERROR')] [string] $Level = 'INFO'
    )

    $line = '{0} [{1}] {2}' -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'), $Level, $Message

    switch ($Level) {
        'ERROR' { Write-Host $line -ForegroundColor Red }
        'WARN'  { Write-Host $line -ForegroundColor Yellow }
        default { Write-Host $line }
    }

    if ($script:LogPath) {
        try {
            Add-Content -Path $script:LogPath -Value $line -Encoding UTF8
        } catch {
            # A missing log file must not stop the report from being sent.
        }
    }
}

# --------------------------------------------------------------------------
# Collection
#
# Each getter returns $null rather than throwing when a value cannot be read,
# because a machine missing one field should still report the rest. Only
# hostname is required by the server.
# --------------------------------------------------------------------------

function Get-CimValue {
    param(
        [Parameter(Mandatory)] [string] $ClassName,
        [Parameter(Mandatory)] [string] $Property
    )

    try {
        $instance = Get-CimInstance -ClassName $ClassName -ErrorAction Stop | Select-Object -First 1

        if ($null -eq $instance) {
            return $null
        }

        $value = $instance.$Property

        if ($null -eq $value) {
            return $null
        }

        $text = ([string] $value).Trim()

        if ($text -eq '') {
            return $null
        }

        return $text
    } catch {
        Write-Log "could not read $ClassName.$Property : $($_.Exception.Message)" 'WARN'
        return $null
    }
}

function Get-MachineUuid {
    # The SMBIOS UUID survives a reinstall, which is what makes it the most
    # reliable key for matching a machine the server already knows about.
    $uuid = Get-CimValue -ClassName 'Win32_ComputerSystemProduct' -Property 'UUID'

    # Some machines report an all-zero or all-F placeholder rather than nothing.
    if ($uuid -and $uuid -notmatch '^[0\-]+$' -and $uuid -notmatch '^[Ff\-]+$') {
        return $uuid
    }

    return $null
}

function Get-PrimaryAdapter {
    # The adapter carrying the default route, not simply the first one that is
    # up: a machine with a VPN, a Hyper-V switch, or a docking NIC has several.
    try {
        $config = Get-NetIPConfiguration -ErrorAction Stop |
            Where-Object { $null -ne $_.IPv4DefaultGateway } |
            Select-Object -First 1

        if ($null -ne $config) {
            $ipv4 = $config.IPv4Address | Select-Object -First 1

            return [pscustomobject]@{
                IpAddress  = if ($null -ne $ipv4) { $ipv4.IPAddress } else { $null }
                MacAddress = $config.NetAdapter.MacAddress
            }
        }
    } catch {
        Write-Log "Get-NetIPConfiguration unavailable, falling back to WMI: $($_.Exception.Message)" 'WARN'
    }

    # Server 2012 and older do not have the NetTCPIP module.
    try {
        $legacy = Get-CimInstance -ClassName 'Win32_NetworkAdapterConfiguration' -ErrorAction Stop |
            Where-Object { $_.IPEnabled -and $_.DefaultIPGateway } |
            Select-Object -First 1

        if ($null -ne $legacy) {
            $address = $legacy.IPAddress | Where-Object { $_ -notmatch ':' } | Select-Object -First 1

            return [pscustomobject]@{
                IpAddress  = $address
                MacAddress = $legacy.MACAddress
            }
        }
    } catch {
        Write-Log "could not determine the primary adapter: $($_.Exception.Message)" 'WARN'
    }

    return [pscustomobject]@{ IpAddress = $null; MacAddress = $null }
}

function Get-RamGb {
    $bytes = Get-CimValue -ClassName 'Win32_ComputerSystem' -Property 'TotalPhysicalMemory'

    if (-not $bytes) {
        return $null
    }

    return [int] [math]::Round([double] $bytes / 1GB, 0)
}

function Get-StorageGb {
    # Fixed local disks only. Mapped network drives and removable media are not
    # this machine's storage and would inflate every report they are attached to.
    try {
        $total = Get-CimInstance -ClassName 'Win32_LogicalDisk' -Filter 'DriveType = 3' -ErrorAction Stop |
            Measure-Object -Property Size -Sum

        if ($total.Sum -gt 0) {
            return [int] [math]::Round($total.Sum / 1GB, 0)
        }
    } catch {
        Write-Log "could not total local disks: $($_.Exception.Message)" 'WARN'
    }

    return $null
}

function Get-InstalledSoftware {
    <#
        Read from the uninstall registry keys rather than Win32_Product.

        Querying Win32_Product makes the installer validate and reconfigure
        every MSI-installed package on the machine. It takes minutes, writes
        1035 events to the event log, and has been known to restart services.
        The registry gives the same list in under a second.
    #>
    $paths = @(
        'HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\*',
        'HKLM:\SOFTWARE\WOW6432Node\Microsoft\Windows\CurrentVersion\Uninstall\*'
    )

    $software = @()

    foreach ($path in $paths) {
        try {
            $entries = Get-ItemProperty -Path $path -ErrorAction SilentlyContinue
        } catch {
            continue
        }

        foreach ($entry in $entries) {
            $name = $null

            if ($entry.PSObject.Properties.Name -contains 'DisplayName') {
                $name = $entry.DisplayName
            }

            if (-not $name) {
                continue
            }

            # Updates and hotfixes would swamp the list without describing what
            # is actually installed.
            if ($entry.PSObject.Properties.Name -contains 'SystemComponent' -and $entry.SystemComponent -eq 1) {
                continue
            }

            if ($entry.PSObject.Properties.Name -contains 'ReleaseType' -and
                $entry.ReleaseType -in @('Security Update', 'Update Rollup', 'Hotfix')) {
                continue
            }

            $version = $null
            if ($entry.PSObject.Properties.Name -contains 'DisplayVersion') {
                $version = $entry.DisplayVersion
            }

            $publisher = $null
            if ($entry.PSObject.Properties.Name -contains 'Publisher') {
                $publisher = $entry.Publisher
            }

            $installed = $null
            if ($entry.PSObject.Properties.Name -contains 'InstallDate') {
                $installed = $entry.InstallDate
            }

            $software += [pscustomobject]@{
                name         = ([string] $name).Trim()
                version      = if ($version) { ([string] $version).Trim() } else { $null }
                publisher    = if ($publisher) { ([string] $publisher).Trim() } else { $null }
                installed_on = if ($installed) { ([string] $installed).Trim() } else { $null }
            }
        }
    }

    return $software |
        Sort-Object -Property name, version -Unique |
        Sort-Object -Property name
}

# --------------------------------------------------------------------------
# Payload
# --------------------------------------------------------------------------

function Build-Payload {
    param([bool] $CollectSoftware)

    $startedAt = Get-Date
    Write-Log 'collecting inventory'

    $adapter = Get-PrimaryAdapter

    $software = @()
    if ($CollectSoftware) {
        $software = @(Get-InstalledSoftware)
        Write-Log "found $($software.Count) installed packages"
    } else {
        Write-Log 'software collection disabled in config'
    }

    $payload = [ordered]@{
        machine_uuid  = Get-MachineUuid
        hostname      = $env:COMPUTERNAME

        ip_address    = $adapter.IpAddress
        mac_address   = $adapter.MacAddress

        manufacturer  = Get-CimValue -ClassName 'Win32_ComputerSystem' -Property 'Manufacturer'
        model         = Get-CimValue -ClassName 'Win32_ComputerSystem' -Property 'Model'
        serial_number = Get-CimValue -ClassName 'Win32_BIOS' -Property 'SerialNumber'

        os_name       = Get-CimValue -ClassName 'Win32_OperatingSystem' -Property 'Caption'
        os_version    = Get-CimValue -ClassName 'Win32_OperatingSystem' -Property 'Version'

        cpu_name      = Get-CimValue -ClassName 'Win32_Processor' -Property 'Name'
        ram_gb        = Get-RamGb
        storage_gb    = Get-StorageGb

        installed_software = $software
    }

    <#
        raw_payload is stored verbatim in computer_snapshots, so it carries only
        this declared set — how the report was produced, not a dump of whatever
        the machine happened to expose. Adding user accounts, licence keys, or
        network shares here would put them in the database permanently.
    #>
    $payload.raw_payload = [ordered]@{
        agent_version       = $AgentVersion
        agent_platform      = 'windows'
        powershell_version  = $PSVersionTable.PSVersion.ToString()
        collected_at        = $startedAt.ToString('o')
        collection_seconds  = [math]::Round(((Get-Date) - $startedAt).TotalSeconds, 2)
        software_count      = $software.Count
    }

    return $payload
}

# --------------------------------------------------------------------------
# Send
# --------------------------------------------------------------------------

function Send-Payload {
    param(
        [Parameter(Mandatory)] $Payload,
        [Parameter(Mandatory)] [string] $Endpoint,
        [Parameter(Mandatory)] [string] $Token,
        [int] $TimeoutSeconds = 30,
        [int] $MaxAttempts = 3
    )

    # Some fleets still default to TLS 1.0, which most servers now refuse.
    try {
        [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    } catch {
        Write-Log 'could not force TLS 1.2; using the system default' 'WARN'
    }

    $body = $Payload | ConvertTo-Json -Depth 6 -Compress
    $headers = @{ Authorization = "Bearer $Token"; Accept = 'application/json' }

    for ($attempt = 1; $attempt -le $MaxAttempts; $attempt++) {
        try {
            $response = Invoke-RestMethod -Uri $Endpoint -Method Post -Headers $headers `
                -ContentType 'application/json; charset=utf-8' `
                -Body ([Text.Encoding]::UTF8.GetBytes($body)) `
                -TimeoutSec $TimeoutSeconds

            Write-Log "accepted: computer_id=$($response.computer_id) hostname=$($response.hostname)"
            return $true
        } catch {
            $status = $null
            $retryAfter = $null

            if ($_.Exception.PSObject.Properties.Name -contains 'Response' -and $null -ne $_.Exception.Response) {
                try {
                    $status = [int] $_.Exception.Response.StatusCode
                    $retryAfter = $_.Exception.Response.Headers['Retry-After']
                } catch {
                    # Older PowerShell exposes these inconsistently; the message
                    # below still carries enough to diagnose the failure.
                }
            }

            # A rejected token or a malformed payload will be rejected again no
            # matter how many times it is sent.
            if ($status -eq 401) {
                Write-Log 'rejected: the token is not accepted. Check config.json, or regenerate the token in the back-office.' 'ERROR'
                return $false
            }

            if ($status -eq 422) {
                Write-Log "rejected: the server refused the payload as invalid. $($_.ErrorDetails.Message)" 'ERROR'
                return $false
            }

            if ($attempt -ge $MaxAttempts) {
                Write-Log "giving up after $MaxAttempts attempts: $($_.Exception.Message)" 'ERROR'
                return $false
            }

            # The server sends Retry-After when it throttles; honouring it is
            # what keeps a fleet from retrying itself into a longer lockout.
            if ($status -eq 429 -and $retryAfter) {
                $wait = [int] $retryAfter
                Write-Log "throttled; the server asked for $wait seconds" 'WARN'
            } else {
                $wait = [math]::Pow(2, $attempt) * 5
                Write-Log "attempt $attempt failed ($($_.Exception.Message)); retrying in $wait seconds" 'WARN'
            }

            Start-Sleep -Seconds $wait
        }
    }

    return $false
}

# --------------------------------------------------------------------------
# Main
# --------------------------------------------------------------------------

try {
    if (-not (Test-Path -LiteralPath $ConfigPath)) {
        throw "config not found at $ConfigPath. Copy config.example.json to config.json and fill in the endpoint and token."
    }

    $config = Get-Content -LiteralPath $ConfigPath -Raw -Encoding UTF8 | ConvertFrom-Json

    if ($config.PSObject.Properties.Name -contains 'log_path' -and $config.log_path) {
        $script:LogPath = $config.log_path

        $logDirectory = Split-Path -Parent $script:LogPath
        if ($logDirectory -and -not (Test-Path -LiteralPath $logDirectory)) {
            New-Item -ItemType Directory -Path $logDirectory -Force | Out-Null
        }
    }

    if (-not $config.endpoint -or -not $config.token) {
        throw 'config.json must set both endpoint and token.'
    }

    if ($config.token -like '*REPLACE-WITH-THE-TOKEN*') {
        throw 'config.json still holds the placeholder token from config.example.json.'
    }

    $timeout = 30
    if ($config.PSObject.Properties.Name -contains 'timeout_seconds' -and $config.timeout_seconds) {
        $timeout = [int] $config.timeout_seconds
    }

    $attempts = 3
    if ($config.PSObject.Properties.Name -contains 'max_attempts' -and $config.max_attempts) {
        $attempts = [int] $config.max_attempts
    }

    $collectSoftware = $true
    if ($config.PSObject.Properties.Name -contains 'collect_software') {
        $collectSoftware = [bool] $config.collect_software
    }

    $payload = Build-Payload -CollectSoftware $collectSoftware

    if ($DryRun) {
        Write-Log 'dry run: printing the payload instead of sending it'
        $payload | ConvertTo-Json -Depth 6
        exit 0
    }

    $sent = Send-Payload -Payload $payload -Endpoint $config.endpoint -Token $config.token `
        -TimeoutSeconds $timeout -MaxAttempts $attempts

    if (-not $sent) {
        exit 1
    }

    exit 0
} catch {
    Write-Log $_.Exception.Message 'ERROR'
    exit 1
}
