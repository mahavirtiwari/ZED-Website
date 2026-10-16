<#
.SYNOPSIS
    STEP 5 - on the staging server, once. Gets a free Let's Encrypt certificate
    for zedcms.umon.in with win-acme, adds the HTTPS binding in IIS and turns on
    HTTPS everywhere (redirect, secure cookies, HSTS).

.DESCRIPTION
    Before running: the site must already answer on http://zedcms.umon.in/
    from the internet (DNS points to this server; port 80 open in the Azure
    network security group), because Let's Encrypt checks it over port 80.
    win-acme also creates a Windows scheduled task that renews the certificate
    automatically before it expires (every ~60 days).

    Already have a certificate (for example *.umon.in)? Import it into the
    server's certificate store, add the https binding for zedcms.umon.in in IIS
    Manager, then run this script with -SkipCertificate.

.PARAMETER Email
    Address Let's Encrypt writes to about expiring certificates.
.PARAMETER SkipCertificate
    Only switch the website to HTTPS (certificate already bound in IIS).

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Enable-ZedHttps.ps1 -Email you@example.com
#>
param(
    [string]$Email,
    [switch]$SkipCertificate,
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

try {
    $site = Get-Website -Name $SiteName
    if (-not $site) { throw "IIS website $SiteName not found. Run Deploy-ZedSite.ps1 first." }
    $envFile = Join-Path $SiteRoot 'shared\.env'
    $current = Join-Path $SiteRoot 'current'

    if (-not $SkipCertificate) {
        if (-not $Email) { $Email = Read-Host 'E-mail address for Let''s Encrypt expiry notices' }

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
        Write-Ok 'certificate installed; automatic renewal scheduled (Task Scheduler: win-acme renew)'
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
    Write-Host ''
    Write-Host "HTTPS is on: https://$HostName/" -ForegroundColor Green
} finally {
    Stop-ZedLog
}
