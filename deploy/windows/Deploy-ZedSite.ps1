<#
.SYNOPSIS
    STEP 4 - on the staging server, for the first install and for every update.
    Installs this release package as the zedcms.umon.in website in IIS.

.DESCRIPTION
    Run it from the unzipped release package (scripts\Deploy-ZedSite.ps1), in
    PowerShell opened with "Run as administrator". It:
      1. copies the website into C:\inetpub\zedcms\releases\<date-time>
      2. links the parts that must survive updates, kept in C:\inetpub\zedcms\shared:
           .env (settings), storage\ (logs, sessions, cache) and
           media\ (uploads, slider, photogallery - files added through the CMS)
      3. installs the PHP libraries (composer install), runs the database
         migrations and builds Laravel's caches
      4. creates or updates the IIS app pool and website, then switches
         C:\inetpub\zedcms\current to the new release (the IIS site points at
         current\public) and keeps the last 3 releases for rollback
      5. checks that the home page and the CMS login answer

    If a step fails, the site keeps running the previous release.

.PARAMETER MediaZip
    ZED-media-*.zip from Export-ZedMedia.ps1 (uploaded images, slider and gallery
    photos). Files are added to shared\media; existing newer files are kept.
.PARAMETER AdminEmail
    Also creates (or resets the password of) this CMS superadmin; you type the password.
.PARAMETER HttpsEmail
    After deploying, also gets the free Let's Encrypt SSL certificate and turns
    on HTTPS (runs Enable-ZedHttps.ps1 -Email <address>), if HTTPS is not on yet.
.PARAMETER Rollback
    Switches back to the previous release instead of installing a new one.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Deploy-ZedSite.ps1 -MediaZip C:\ZED-setup\ZED-media-20261016-1500.zip -AdminEmail you@example.com -HttpsEmail you@example.com
.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Deploy-ZedSite.ps1
.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Deploy-ZedSite.ps1 -Rollback
#>
param(
    [string]$MediaZip,
    [string]$AdminEmail,
    [string]$HttpsEmail,
    [switch]$Rollback,
    [string]$HostName = 'zedcms.umon.in',
    [string]$SiteRoot = 'C:\inetpub\zedcms',
    [string]$SiteName = 'zedcms',
    [string]$PoolName = 'zedcms',
    [int]$KeepReleases = 3
)

. "$PSScriptRoot\ZedCommon.ps1"
Assert-Admin
Start-ZedLog 'deploy'
Import-Module WebAdministration

$package = Split-Path -Parent $PSScriptRoot
$releases = Join-Path $SiteRoot 'releases'
$shared = Join-Path $SiteRoot 'shared'
$current = Join-Path $SiteRoot 'current'
$envFile = Join-Path $shared '.env'
$media = Join-Path $shared 'media'
$mediaFolders = 'uploads', 'slider', 'photogallery'
$poolUser = "IIS AppPool\$PoolName"

function Invoke-Artisan([string]$Release, [string[]]$Arguments) {
    Push-Location $Release
    try { Invoke-Native $php (@('artisan') + $Arguments) } finally { Pop-Location }
}

function Switch-Current([string]$Release) {
    Remove-Link $current
    New-Junction $current $Release
    if ((Get-WebAppPoolState -Name $PoolName).Value -eq 'Started') { Restart-WebAppPool -Name $PoolName }
    else { Start-WebAppPool -Name $PoolName }
}

function Remove-Release([string]$Release) {
    # unlink shared folders first, so deleting the release never touches them
    Remove-Link (Join-Path $Release '.env')
    Remove-Link (Join-Path $Release 'storage')
    foreach ($folder in $mediaFolders) { Remove-Link (Join-Path $Release "public\$folder") }
    cmd /c rmdir /s /q "`"$Release`"" | Out-Null
}

function Test-Site {
    Write-Step "Checking http://$HostName/"
    $curl = Get-Command curl.exe -ErrorAction SilentlyContinue
    if (-not $curl) { Write-Warning 'curl.exe not found; open the site in a browser to check it.'; return }
    $scheme = 'http'
    if ((Get-EnvValue $envFile 'APP_URL') -like 'https://*') { $scheme = 'https' }
    $port = 80
    if ($scheme -eq 'https') { $port = 443 }
    foreach ($path in '/', '/cms/login') {
        # ask this server directly (127.0.0.1), with the site's host name
        $code = & curl.exe -s -k -o NUL -w '%{http_code}' --max-time 60 --resolve "${HostName}:${port}:127.0.0.1" "${scheme}://$HostName$path"
        if ($code -eq '200') { Write-Ok "$path -> HTTP $code" }
        else { Write-Warning "$path -> HTTP $code. See $current\storage\logs\laravel.log and C:\PHP\logs\php-errors.log." }
    }
}

try {
    $php = Get-PhpExe
    New-Item -ItemType Directory -Force -Path $releases, $shared | Out-Null

    # --- Rollback ---------------------------------------------------------------------
    if ($Rollback) {
        $active = $null
        if (Test-Path $current) { $active = (Get-Item $current).Target | Select-Object -First 1 }
        $previous = Get-ChildItem $releases -Directory | Sort-Object Name -Descending |
            Where-Object { $_.FullName -ne $active } | Select-Object -First 1
        if (-not $previous) { throw 'No previous release to go back to.' }
        Write-Step "Switching back to $($previous.Name)"
        Switch-Current $previous.FullName
        Invoke-Artisan $previous.FullName @('optimize')
        Test-Site
        Write-Host 'Rolled back. Note: database migrations are not undone.' -ForegroundColor Yellow
        return
    }

    if (-not (Test-Path (Join-Path $package 'app\artisan'))) {
        throw "Run this script from the unzipped release package (expected $package\app\artisan)."
    }
    Write-Host (Get-Content (Join-Path $package 'RELEASE.txt') -ErrorAction SilentlyContinue | Out-String)

    # --- 1. Settings --------------------------------------------------------------------
    Write-Step 'Settings (shared\.env)'
    $envFile = Initialize-SharedEnv $SiteRoot (Join-Path $package 'app\.env.example') $HostName
    if (-not (Get-EnvValue $envFile 'DB_PASSWORD')) {
        throw "No database settings in $envFile. Run scripts\Initialize-ZedDatabase.ps1 first."
    }
    if (-not (Get-EnvValue $envFile 'APP_KEY')) { Set-EnvValue $envFile 'APP_KEY' (New-LaravelKey) }
    $https = (Get-EnvValue $envFile 'APP_URL') -like 'https://*'
    Write-Ok "APP_URL = $(Get-EnvValue $envFile 'APP_URL')"

    # --- 2. Copy the release ----------------------------------------------------------------
    $release = Join-Path $releases (Get-Date -Format 'yyyyMMdd-HHmmss')
    Write-Step "Copying the website to $release"
    & robocopy (Join-Path $package 'app') $release /E /NFL /NDL /NJH /NJS /NP /R:2 /W:2 | Out-Null
    if ($LASTEXITCODE -ge 8) { throw "Copying the website failed (robocopy exit code $LASTEXITCODE)." }
    & robocopy (Join-Path $package 'assets') (Join-Path $release 'public\assets') /E /NFL /NDL /NJH /NJS /NP /R:2 /W:2 /XF *.php | Out-Null
    if ($LASTEXITCODE -ge 8) { throw "Copying assets failed (robocopy exit code $LASTEXITCODE)." }
    Copy-Item (Join-Path $package 'RELEASE.txt') $release -ErrorAction SilentlyContinue

    # --- 3. Shared folders -----------------------------------------------------------------
    Write-Step 'Linking settings, storage and uploaded files'
    $storage = Join-Path $shared 'storage'
    foreach ($dir in 'app\public', 'app\private', 'framework\cache\data', 'framework\sessions', 'framework\views', 'logs') {
        New-Item -ItemType Directory -Force -Path (Join-Path $storage $dir) | Out-Null
    }
    Remove-Item -Recurse -Force (Join-Path $release 'storage')
    New-Junction (Join-Path $release 'storage') $storage
    New-Item -ItemType SymbolicLink -Path (Join-Path $release '.env') -Target $envFile | Out-Null

    foreach ($folder in $mediaFolders) { New-Item -ItemType Directory -Force -Path (Join-Path $media $folder) | Out-Null }
    if ($MediaZip) {
        if (-not (Test-Path $MediaZip)) { throw "Media package not found: $MediaZip" }
        Write-Host "    adding files from $MediaZip"
        $tmp = Join-Path $ZedDefaults.SetupDir 'media-unpacked'
        Expand-Zip $MediaZip $tmp
        foreach ($folder in $mediaFolders) {
            if (Test-Path (Join-Path $tmp $folder)) {
                & robocopy (Join-Path $tmp $folder) (Join-Path $media $folder) /E /XO /NFL /NDL /NJH /NJS /NP /XF *.php *.phtml *.phar | Out-Null
                if ($LASTEXITCODE -ge 8) { throw "Copying $folder failed (robocopy exit code $LASTEXITCODE)." }
            }
        }
        Remove-Item -Recurse -Force $tmp
    }
    foreach ($folder in $mediaFolders) {
        $count = (Get-ChildItem -Recurse -File (Join-Path $media $folder) -ErrorAction SilentlyContinue).Count
        New-Junction (Join-Path $release "public\$folder") (Join-Path $media $folder)
        Write-Ok "$folder : $count files"
    }

    # --- 4. PHP libraries, database changes, caches ---------------------------------------
    Write-Step 'Installing PHP libraries (composer install)'
    Push-Location $release
    try {
        Invoke-Native $php @((Join-Path (Split-Path $php) 'composer.phar'), 'install', '--no-dev', '--optimize-autoloader', '--no-interaction', '--no-progress')
    } finally { Pop-Location }

    Write-Step 'Database migrations'
    Invoke-Artisan $release @('migrate', '--force')

    Write-Step 'Building caches (config, routes, views)'
    Invoke-Artisan $release @('optimize:clear')
    Invoke-Artisan $release @('optimize')

    # HTTPS redirect in web.config once the site has a certificate
    if ($https) {
        $webConfig = Join-Path $release 'public\web.config'
        $xml = [IO.File]::ReadAllText($webConfig)
        $xml = $xml.Replace('name="ZED: HTTPS only" enabled="false"', 'name="ZED: HTTPS only" enabled="true"')
        [IO.File]::WriteAllText($webConfig, $xml, (New-Object Text.UTF8Encoding $false))
        Write-Ok 'HTTP -> HTTPS redirect on'
    }

    # --- 5. IIS ---------------------------------------------------------------------------
    Write-Step "IIS app pool and website ($SiteName, http://$HostName)"
    if (-not (Test-Path "IIS:\AppPools\$PoolName")) {
        New-WebAppPool -Name $PoolName | Out-Null
    }
    Set-ItemProperty "IIS:\AppPools\$PoolName" -Name managedRuntimeVersion -Value ''
    Set-ItemProperty "IIS:\AppPools\$PoolName" -Name processModel.identityType -Value 'ApplicationPoolIdentity'
    Set-ItemProperty "IIS:\AppPools\$PoolName" -Name startMode -Value 'AlwaysRunning'

    # file permissions for the app pool: read the code, write storage, uploads and Laravel's cache
    Invoke-Native 'icacls.exe' @($release, '/grant', "${poolUser}:(OI)(CI)RX", '/T', '/Q')
    Invoke-Native 'icacls.exe' @($envFile, '/grant', "${poolUser}:R", '/Q')
    Invoke-Native 'icacls.exe' @($storage, '/grant', "${poolUser}:(OI)(CI)M", '/T', '/Q')
    Invoke-Native 'icacls.exe' @($media, '/grant', "${poolUser}:(OI)(CI)M", '/T', '/Q')
    Invoke-Native 'icacls.exe' @((Join-Path $release 'bootstrap\cache'), '/grant', "${poolUser}:(OI)(CI)M", '/T', '/Q')

    $firstInstall = -not (Test-Path $current)
    if ($firstInstall) { New-Junction $current $release }
    $physical = Join-Path $current 'public'
    if (-not (Get-Website -Name $SiteName)) {
        New-Website -Name $SiteName -PhysicalPath $physical -ApplicationPool $PoolName -HostHeader $HostName -Port 80 | Out-Null
        Write-Ok "created website $SiteName"
    } else {
        Set-ItemProperty "IIS:\Sites\$SiteName" -Name physicalPath -Value $physical
        Set-ItemProperty "IIS:\Sites\$SiteName" -Name applicationPool -Value $PoolName
        if (-not (Get-WebBinding -Name $SiteName -Protocol http -HostHeader $HostName)) {
            New-WebBinding -Name $SiteName -Protocol http -Port 80 -HostHeader $HostName
        }
    }
    if ((Get-Website -Name $SiteName).State -ne 'Started') { Start-Website -Name $SiteName }

    # --- 6. Go live ---------------------------------------------------------------------------
    Write-Step 'Switching the website to the new release'
    Switch-Current $release
    Write-Ok "current -> $release"

    Get-ChildItem $releases -Directory | Sort-Object Name -Descending | Select-Object -Skip $KeepReleases |
        ForEach-Object { Write-Host "    removing old release $($_.Name)"; Remove-Release $_.FullName }

    if ($AdminEmail) {
        Write-Step "CMS superadmin $AdminEmail"
        Invoke-Artisan $release @('zed:cms-admin', $AdminEmail)
    }

    Test-Site
    if ($HttpsEmail -and -not $https) {
        Write-Step 'Free SSL certificate (Let''s Encrypt)'
        try {
            & (Join-Path $PSScriptRoot 'Enable-ZedHttps.ps1') -Email $HttpsEmail -HostName $HostName -SiteRoot $SiteRoot -SiteName $SiteName -PoolName $PoolName
        } catch {
            # the new release is live on http; only the certificate step failed
            Write-Warning "The website is deployed, but the SSL certificate step failed: $($_.Exception.Message)"
            Write-Warning 'Fix the cause (usually port 80 closed in Azure) and run scripts\Enable-ZedHttps.ps1 -Email <address>.'
        }
        $https = (Get-EnvValue $envFile 'APP_URL') -like 'https://*'
    }
    Write-Host ''
    Write-Host "Deployed. Website: $(Get-EnvValue $envFile 'APP_URL')/   CMS: $(Get-EnvValue $envFile 'APP_URL')/cms" -ForegroundColor Green
    if (-not $https) { Write-Host 'Next: scripts\Enable-ZedHttps.ps1 -Email you@example.com (free Let''s Encrypt SSL certificate).' }
} catch {
    Write-Host ''
    Write-Host "Deployment stopped: $($_.Exception.Message)" -ForegroundColor Red
    if ($release -and (Test-Path $release) -and (Test-Path $current) -and ((Get-Item $current).Target -notcontains $release)) {
        Write-Host 'The website still runs the previous release. Removing the unfinished copy.'
        Remove-Release $release
    }
    throw
} finally {
    Stop-ZedLog
}
