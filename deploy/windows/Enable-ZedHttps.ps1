<#
.SYNOPSIS
    STEP 5 - on the staging server, once. Gets a FREE SSL certificate from
    Let's Encrypt for zedcms.umon.in with win-acme, adds the HTTPS binding in
    IIS and turns on HTTPS everywhere (redirect, secure cookies, HSTS).

.DESCRIPTION
    Free certificate: Let's Encrypt certificates cost nothing and are valid for
    90 days (shorter in future). win-acme installs a Windows scheduled task
    that checks every day and renews the certificate automatically about 30
    days before it expires, then updates the IIS binding itself - nothing to
    do by hand. Let's Encrypt no longer sends expiry e-mails, so check it now
    and then with -Status (shows the expiry date and the renewal task).

    Before the first run: the site must already answer on http://zedcms.umon.in/
    from the internet (DNS points to this server; port 80 open in the Azure
    network security group), because Let's Encrypt checks it over port 80.
    Keep port 80 open afterwards too: every renewal is checked the same way.

    Already have a paid certificate (for example *.umon.in)? Import it into the
    server's certificate store, add the https binding for zedcms.umon.in in IIS
    Manager, then run this script with -SkipCertificate.

.PARAMETER Email
    Address for the Let's Encrypt account (used if Let's Encrypt must contact you).
.PARAMETER SkipCertificate
    Only switch the website to HTTPS (certificate already bound in IIS).
.PARAMETER Status
    Only show the certificate's expiry date and the automatic renewal task
    (recreates the task if it is missing).

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Enable-ZedHttps.ps1 -Email you@example.com
.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Enable-ZedHttps.ps1 -Status
#>
param(
    [string]$Email,
    [switch]$SkipCertificate,
    [switch]$Status,
    [string]$HostName = 'zedcms.umon.in',
    [string]$SiteRoot = 'C:\inetpub\zedcms',
    [string]$SiteName = 'zedcms',
    [string]$PoolName = 'zedcms',
    [string]$WinAcmeUrl = 'https://github.com/win-acme/win-acme/releases/download/v2.2.9.1701/win-acme.v2.2.9.1701.x64.pluggable.zip',
    [string]$WinAcmeDir = 'C:\win-acme'
)

. "$PSScriptRoot\ZedCommon.ps1"
Assert-Admin
Start-ZedLog 'https'
Import-Module WebAdministration

# win-acme renews through a Windows scheduled task; recreate it if it is missing.
function Get-RenewalTask { Get-ScheduledTask -ErrorAction SilentlyContinue | Where-Object { $_.TaskName -like 'win-acme*' } | Select-Object -First 1 }

function Confirm-RenewalTask {
    if (-not (Get-RenewalTask)) {
        Write-Host '    renewal task missing; creating it'
        Invoke-Native (Join-Path $WinAcmeDir 'wacs.exe') @('--setuptaskscheduler', '--closeonfinish')
    }
    $task = Get-RenewalTask
    if (-not $task) { throw 'The win-acme renewal task could not be created. Open C:\win-acme\wacs.exe and choose "More options > (Re)create scheduled task".' }
    $info = $task | Get-ScheduledTaskInfo
    Write-Ok "automatic renewal: task '$($task.TaskName)', next run $($info.NextRunTime)"
}

function Show-CertificateStatus {
    Write-Step "Certificate for $HostName"
    $binding = Get-WebBinding -Name $SiteName -Protocol https | Select-Object -First 1
    if (-not $binding) { Write-Warning 'No https binding: HTTPS is not set up yet.'; return }
    $cert = $null
    if ($binding.certificateHash) {
        $store = if ($binding.certificateStoreName) { $binding.certificateStoreName } else { 'My' }
        $cert = Get-Item "Cert:\LocalMachine\$store\$($binding.certificateHash)" -ErrorAction SilentlyContinue
    }
    if (-not $cert) { Write-Warning 'The https binding has no certificate.'; return }
    $days = [math]::Floor(($cert.NotAfter - (Get-Date)).TotalDays)
    Write-Ok "issued by: $($cert.Issuer)"
    Write-Ok "valid until: $($cert.NotAfter.ToString('dd MMM yyyy HH:mm')) ($days days left)"
    if ($days -lt 15) { Write-Warning 'Less than 15 days left: automatic renewal is not working. Run C:\win-acme\wacs.exe --renew and read its output.' }
    if (Test-Path (Join-Path $WinAcmeDir 'wacs.exe')) { Confirm-RenewalTask }
    $task = Get-RenewalTask
    if ($task) {
        $info = $task | Get-ScheduledTaskInfo
        if ($info.LastRunTime -gt [datetime]'2000-01-01') { Write-Ok "last renewal check: $($info.LastRunTime) (result code $($info.LastTaskResult); 0 = OK)" }
    }
}

try {
    $site = Get-Website -Name $SiteName
    if ($Status) {
        if (-not $site) { throw "IIS website $SiteName not found." }
        Show-CertificateStatus
        return
    }
    if (-not $site) { throw "IIS website $SiteName not found. Run Deploy-ZedSite.ps1 first." }
    $envFile = Join-Path $SiteRoot 'shared\.env'
    $current = Join-Path $SiteRoot 'current'

    if (-not $SkipCertificate) {
        if (-not $Email) { $Email = Read-Host 'E-mail address for the Let''s Encrypt account' }

        Write-Step 'Checking that the site is reachable from the internet'
        try {
            $response = Invoke-WebRequest -Uri "http://$HostName/" -UseBasicParsing -MaximumRedirection 0 -TimeoutSec 30 -ErrorAction Stop
            Write-Ok "http://$HostName/ -> HTTP $($response.StatusCode)"
        } catch {
            Write-Warning "Could not open http://$HostName/ from this server ($($_.Exception.Message))."
            Write-Warning 'Some Azure networks do not allow a server to reach its own public address; continuing.'
        }

        Write-Step 'win-acme (Let''s Encrypt client)'
        if (-not (Test-Path (Join-Path $WinAcmeDir 'wacs.exe'))) {
            $zip = Get-Download $WinAcmeUrl (Split-Path -Leaf $WinAcmeUrl)
            New-Item -ItemType Directory -Force -Path $WinAcmeDir | Out-Null
            Add-Type -AssemblyName System.IO.Compression.FileSystem
            [IO.Compression.ZipFile]::ExtractToDirectory($zip, $WinAcmeDir)
        }
        Write-Ok "installed in $WinAcmeDir"

        Write-Step "Requesting the certificate for $HostName"
        Invoke-Native (Join-Path $WinAcmeDir 'wacs.exe') @(
            '--source', 'iis', '--siteid', "$($site.Id)", '--host', $HostName,
            '--installation', 'iis', '--installationsiteid', "$($site.Id)",
            '--emailaddress', $Email, '--accepttos', '--closeonfinish'
        )
        Write-Ok 'certificate installed'
        Confirm-RenewalTask
    }

    if (-not (Get-WebBinding -Name $SiteName -Protocol https)) {
        throw "The website has no https binding. Add the certificate in IIS Manager (Bindings... > https, host name $HostName), then run this script with -SkipCertificate."
    }

    Write-Step 'Switching the website to HTTPS'
    Set-EnvValue $envFile 'APP_URL' "https://$HostName"
    Set-EnvValue $envFile 'SESSION_SECURE_COOKIE' 'true'
    $webConfig = Join-Path $current 'public\web.config'
    $xml = [IO.File]::ReadAllText($webConfig)
    $xml = $xml.Replace('name="ZED: HTTPS only" enabled="false"', 'name="ZED: HTTPS only" enabled="true"')
    [IO.File]::WriteAllText($webConfig, $xml, (New-Object Text.UTF8Encoding $false))
    Push-Location $current
    try { Invoke-Native (Get-PhpExe) @('artisan', 'optimize') } finally { Pop-Location }
    Restart-WebAppPool -Name $PoolName
    Write-Ok "APP_URL = https://$HostName, secure cookies on, http redirects to https"

    $curl = Get-Command curl.exe -ErrorAction SilentlyContinue
    if ($curl) {
        $code = & curl.exe -s -o NUL -w '%{http_code}' --max-time 60 --resolve "${HostName}:443:127.0.0.1" "https://$HostName/"
        Write-Ok "https://$HostName/ -> HTTP $code"
    }
    Show-CertificateStatus
    Write-Host ''
    Write-Host "HTTPS is on: https://$HostName/ (free Let's Encrypt certificate, renewed automatically)" -ForegroundColor Green
} finally {
    Stop-ZedLog
}
