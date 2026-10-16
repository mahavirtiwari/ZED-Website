<#
.SYNOPSIS
    STEP 2 - on the staging server, once. Installs what the ZED website needs:
    IIS (+ CGI), IIS URL Rewrite, Visual C++ runtime, PHP 8.4 (FastCGI),
    Composer and MySQL 8.4 (as a Windows service).

.DESCRIPTION
    Run in PowerShell opened with "Run as administrator". Safe to run again:
    parts that are already installed are skipped (PHP is updated to the latest
    8.4 release; php.ini settings are re-applied).
    Downloads go to C:\ZED-setup\downloads, the log to C:\ZED-setup\logs.

.PARAMETER SkipMySql
    Use when MySQL 8.x is already installed on this server (or the database is
    on another server).
.PARAMETER MySqlVersion
    MySQL 8.4 LTS release to install (see https://dev.mysql.com/downloads/mysql/8.4.html).

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Install-ZedServer.ps1
.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Install-ZedServer.ps1 -SkipMySql
#>
param(
    [string]$PhpVersion = '8.4',
    [string]$PhpDir = 'C:\PHP',
    [switch]$SkipMySql,
    [string]$MySqlVersion = '8.4.11',
    [string]$MySqlDir = 'C:\MySQL',
    [string]$MySqlService = 'MySQL84'
)

. "$PSScriptRoot\ZedCommon.ps1"
Assert-Admin
Start-ZedLog 'install-server'
$appcmd = Join-Path $env:windir 'system32\inetsrv\appcmd.exe'
$rebootNeeded = $false

try {
    # --- 1. IIS --------------------------------------------------------------------
    Write-Step 'IIS web server with CGI (FastCGI for PHP)'
    $features = 'Web-Server', 'Web-WebServer', 'Web-Default-Doc', 'Web-Static-Content', 'Web-Http-Errors',
                'Web-Http-Redirect', 'Web-Http-Logging', 'Web-Request-Monitor', 'Web-Filtering',
                'Web-Stat-Compression', 'Web-Dyn-Compression', 'Web-CGI', 'Web-Mgmt-Console', 'Web-Scripting-Tools'
    if (Get-Command Install-WindowsFeature -ErrorAction SilentlyContinue) {
        $result = Install-WindowsFeature -Name $features -IncludeManagementTools
        if ($result.RestartNeeded -eq 'Yes') { $rebootNeeded = $true }
    } else {
        # Windows 10/11 (test machines)
        Enable-WindowsOptionalFeature -Online -All -NoRestart -FeatureName IIS-WebServerRole, IIS-WebServer, IIS-CGI,
            IIS-DefaultDocument, IIS-StaticContent, IIS-HttpErrors, IIS-HttpRedirect, IIS-HttpLogging, IIS-RequestFiltering,
            IIS-HttpCompressionStatic, IIS-HttpCompressionDynamic, IIS-ManagementConsole, IIS-ManagementScriptingTools | Out-Null
    }
    Write-Ok 'IIS ready'

    # --- 2. Visual C++ runtime (PHP and MySQL need it) ---------------------------------
    Write-Step 'Microsoft Visual C++ 2015-2022 runtime (x64)'
    $vc = Get-Download 'https://aka.ms/vs/17/release/vc_redist.x64.exe' 'vc_redist.x64.exe'
    $p = Start-Process -FilePath $vc -ArgumentList '/install', '/quiet', '/norestart' -Wait -PassThru
    if ($p.ExitCode -eq 3010) { $rebootNeeded = $true }
    elseif ($p.ExitCode -notin 0, 1638) { throw "Visual C++ runtime setup failed (exit code $($p.ExitCode))." }
    Write-Ok 'installed'

    # --- 3. URL Rewrite -------------------------------------------------------------------
    Write-Step 'IIS URL Rewrite 2.1'
    if (Test-Path 'HKLM:\SOFTWARE\Microsoft\IIS Extensions\URL Rewrite') {
        Write-Ok 'already installed'
    } else {
        $msi = Get-Download 'https://download.microsoft.com/download/1/2/8/128E2E22-C1B9-44A4-BE2A-5859ED1D4592/rewrite_amd64_en-US.msi' 'rewrite_amd64_en-US.msi'
        $p = Start-Process msiexec.exe -ArgumentList '/i', "`"$msi`"", '/qn', '/norestart' -Wait -PassThru
        if ($p.ExitCode -notin 0, 3010) { throw "URL Rewrite setup failed (exit code $($p.ExitCode))." }
        Write-Ok 'installed'
    }

    # --- 4. PHP -----------------------------------------------------------------------------
    Write-Step "PHP $PhpVersion (non-thread-safe x64, for IIS FastCGI)"
    $releases = Invoke-RestMethod -Uri 'https://downloads.php.net/~windows/releases/releases.json' -UseBasicParsing
    $release = $releases.$PhpVersion
    if (-not $release) { throw "PHP $PhpVersion not found in the windows.php.net release list." }
    $build = $release.'nts-vs17-x64'
    if (-not $build) { $build = $release.'nts-vs16-x64' }
    $zipName = $build.zip.path
    Write-Host "    latest release: $($release.version) ($zipName)"
    $phpZip = Get-Download ("https://downloads.php.net/~windows/releases/$zipName") $zipName

    $installed = $null
    if (Test-Path "$PhpDir\php.exe") { $installed = (& "$PhpDir\php.exe" -r 'echo PHP_VERSION;') }
    if ($installed -eq $release.version) {
        Write-Ok "PHP $installed already installed in $PhpDir"
    } else {
        $tmp = Join-Path $ZedDefaults.SetupDir 'php-new'
        Expand-Zip $phpZip $tmp
        $iisRunning = (Get-Service W3SVC -ErrorAction SilentlyContinue).Status -eq 'Running'
        if ($iisRunning) { Invoke-Native 'iisreset.exe' @('/stop') }
        New-Item -ItemType Directory -Force -Path $PhpDir | Out-Null
        & robocopy $tmp $PhpDir /E /NFL /NDL /NJH /NJS /NP /XF php.ini | Out-Null
        if ($LASTEXITCODE -ge 8) { throw "Copying PHP failed (robocopy exit code $LASTEXITCODE)." }
        Remove-Item -Recurse -Force $tmp
        if ($iisRunning) { Invoke-Native 'iisreset.exe' @('/start') }
        Write-Ok "PHP $($release.version) installed in $PhpDir"
    }

    # CA certificates, so PHP can call the ZED portal APIs over HTTPS
    New-Item -ItemType Directory -Force -Path "$PhpDir\extras\ssl", "$PhpDir\logs", "$PhpDir\temp" | Out-Null
    Copy-Item (Get-Download 'https://curl.se/ca/cacert.pem' 'cacert.pem') "$PhpDir\extras\ssl\cacert.pem" -Force

    # php.ini
    $ini = Join-Path $PhpDir 'php.ini'
    if (-not (Test-Path $ini)) { Copy-Item (Join-Path $PhpDir 'php.ini-production') $ini }
    $lines = [Collections.Generic.List[string]]([IO.File]::ReadAllLines($ini))
    function Set-Ini([string]$Key, [string]$Value) {
        $pattern = '^\s*;?\s*' + [regex]::Escape($Key) + '\s*='
        for ($i = 0; $i -lt $lines.Count; $i++) {
            if ($lines[$i] -match $pattern) { $lines[$i] = "$Key = $Value"; return }
        }
        $lines.Add("$Key = $Value")
    }
    function Enable-IniLine([string]$Line) {
        $pattern = '^\s*;\s*' + [regex]::Escape($Line) + '\s*$'
        for ($i = 0; $i -lt $lines.Count; $i++) {
            if ($lines[$i].Trim() -eq $Line) { return }
            if ($lines[$i] -match $pattern) { $lines[$i] = $Line; return }
        }
        $lines.Add($Line)
    }
    # absolute: under IIS, PHP's working folder is the website, not C:\PHP
    Set-Ini 'extension_dir' "`"$PhpDir\ext`""
    foreach ($ext in 'curl', 'fileinfo', 'gd', 'intl', 'mbstring', 'openssl', 'pdo_mysql', 'mysqli', 'sodium', 'zip', 'exif') {
        Enable-IniLine "extension=$ext"
    }
    Enable-IniLine 'zend_extension=opcache'
    Set-Ini 'opcache.enable' '1'
    Set-Ini 'opcache.memory_consumption' '192'
    Set-Ini 'opcache.max_accelerated_files' '20000'
    Set-Ini 'cgi.fix_pathinfo' '1'
    Set-Ini 'cgi.force_redirect' '0'
    Set-Ini 'fastcgi.impersonate' '1'
    Set-Ini 'fastcgi.logging' '0'
    Set-Ini 'expose_php' 'Off'
    Set-Ini 'display_errors' 'Off'
    Set-Ini 'log_errors' 'On'
    Set-Ini 'error_log' "`"$PhpDir\logs\php-errors.log`""
    Set-Ini 'memory_limit' '256M'
    Set-Ini 'max_execution_time' '60'
    Set-Ini 'upload_max_filesize' '24M'
    Set-Ini 'post_max_size' '32M'
    Set-Ini 'date.timezone' 'Asia/Kolkata'
    Set-Ini 'upload_tmp_dir' "`"$PhpDir\temp`""
    Set-Ini 'sys_temp_dir' "`"$PhpDir\temp`""
    Set-Ini 'curl.cainfo' "`"$PhpDir\extras\ssl\cacert.pem`""
    Set-Ini 'openssl.cafile' "`"$PhpDir\extras\ssl\cacert.pem`""
    [IO.File]::WriteAllLines($ini, $lines, (New-Object Text.UTF8Encoding $false))
    Add-ToMachinePath $PhpDir
    # the website (IIS app pool users) writes PHP logs and upload temp files here
    Invoke-Native 'icacls.exe' @("$PhpDir\logs", '/grant', 'IIS_IUSRS:(OI)(CI)M', '/Q')
    Invoke-Native 'icacls.exe' @("$PhpDir\temp", '/grant', 'IIS_IUSRS:(OI)(CI)M', '/Q')

    $modules = & "$PhpDir\php.exe" -m
    $missing = 'curl', 'fileinfo', 'gd', 'intl', 'mbstring', 'openssl', 'pdo_mysql', 'zip', 'Zend OPcache' | Where-Object { $modules -notcontains $_ }
    if ($missing) { throw "PHP extensions not loading: $($missing -join ', '). Check $ini." }
    Write-Ok "php.ini configured ($ini)"

    # --- 5. PHP in IIS (FastCGI) ----------------------------------------------------------
    Write-Step 'Registering PHP with IIS'
    $cgi = Join-Path $PhpDir 'php-cgi.exe'
    $fastCgi = (& $appcmd list config /section:system.webServer/fastCgi) -join "`n"
    if ($fastCgi -notmatch [regex]::Escape($cgi)) {
        Invoke-Native $appcmd @('set', 'config', '/section:system.webServer/fastCgi', "/+[fullPath='$cgi',maxInstances='0',instanceMaxRequests='10000',activityTimeout='600',requestTimeout='600']", '/commit:apphost')
        Invoke-Native $appcmd @('set', 'config', '/section:system.webServer/fastCgi', "/+[fullPath='$cgi'].environmentVariables.[name='PHP_FCGI_MAX_REQUESTS',value='10000']", '/commit:apphost')
        Invoke-Native $appcmd @('set', 'config', '/section:system.webServer/fastCgi', "/+[fullPath='$cgi'].environmentVariables.[name='PHPRC',value='$PhpDir']", '/commit:apphost')
    }
    $handlers = (& $appcmd list config /section:system.webServer/handlers) -join "`n"
    if ($handlers -notmatch 'PHP_via_FastCGI') {
        # @start: before the catch-all static file handler
        Invoke-Native $appcmd @('set', 'config', '/section:system.webServer/handlers', "/+[@start,name='PHP_via_FastCGI',path='*.php',verb='GET,HEAD,POST,PUT,PATCH,DELETE,OPTIONS',modules='FastCgiModule',scriptProcessor='$cgi',resourceType='Either',requireAccess='Script']", '/commit:apphost')
    }
    # let the site's web.config set these sections (public\web.config)
    foreach ($section in 'system.webServer/handlers', 'system.webServer/httpErrors') {
        Invoke-Native $appcmd @('unlock', 'config', "/section:$section")
    }
    Write-Ok 'php-cgi.exe registered as FastCGI handler for *.php'

    # --- 6. Composer ---------------------------------------------------------------------
    Write-Step 'Composer (PHP package manager)'
    Copy-Item (Get-Download 'https://getcomposer.org/download/latest-stable/composer.phar' 'composer.phar') "$PhpDir\composer.phar" -Force
    Set-Content -Encoding ascii -Path "$PhpDir\composer.bat" -Value '@php "%~dp0composer.phar" %*'
    Write-Ok ((& "$PhpDir\php.exe" "$PhpDir\composer.phar" --version 2>$null) -join ' ')

    # --- 7. Firewall ---------------------------------------------------------------------
    Write-Step 'Windows firewall: allow HTTP (80) and HTTPS (443)'
    if (-not (Get-NetFirewallRule -DisplayName 'ZED website (HTTP/HTTPS)' -ErrorAction SilentlyContinue)) {
        New-NetFirewallRule -DisplayName 'ZED website (HTTP/HTTPS)' -Direction Inbound -Protocol TCP -LocalPort 80, 443 -Action Allow | Out-Null
    }
    Write-Ok 'done (also allow ports 80 and 443 in the Azure network security group)'

    # --- 8. MySQL ------------------------------------------------------------------------
    if ($SkipMySql) {
        Write-Step 'MySQL skipped (-SkipMySql)'
    } elseif (Get-Service $MySqlService -ErrorAction SilentlyContinue) {
        Write-Step "MySQL service $MySqlService already installed"
        Start-Service $MySqlService
    } else {
        Write-Step "MySQL $MySqlVersion (Windows service $MySqlService)"
        $name = "mysql-$MySqlVersion-winx64"
        try {
            $mysqlZip = Get-Download "https://dev.mysql.com/get/Downloads/MySQL-8.4/$name.zip" "$name.zip"
        } catch {
            $mysqlZip = Get-Download "https://downloads.mysql.com/archives/get/p/23/file/$name.zip" "$name.zip"
        }
        New-Item -ItemType Directory -Force -Path $MySqlDir | Out-Null
        $base = Join-Path $MySqlDir $name
        if (-not (Test-Path "$base\bin\mysqld.exe")) {
            Add-Type -AssemblyName System.IO.Compression.FileSystem
            [IO.Compression.ZipFile]::ExtractToDirectory($mysqlZip, $MySqlDir)
        }
        $data = Join-Path $MySqlDir 'data'
        $myIni = Join-Path $MySqlDir 'my.ini'
        @"
[mysqld]
basedir=$($base -replace '\\', '/')
datadir=$($data -replace '\\', '/')
port=3306
bind-address=127.0.0.1
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
max_allowed_packet=64M
log-error=$((Join-Path $MySqlDir 'mysql-error.log') -replace '\\', '/')

[client]
port=3306
default-character-set=utf8mb4
"@ | Set-Content -Encoding ascii $myIni
        if (-not (Test-Path $data)) {
            Invoke-Native "$base\bin\mysqld.exe" @("--defaults-file=$myIni", '--initialize-insecure')
        }
        Invoke-Native "$base\bin\mysqld.exe" @('--install', $MySqlService, "--defaults-file=$myIni")
        Start-Service $MySqlService
        Set-Service $MySqlService -StartupType Automatic
        [Environment]::SetEnvironmentVariable('ZED_MYSQL_BIN', "$base\bin", 'Machine')
        Add-ToMachinePath "$base\bin"

        Write-Host ''
        Write-Host 'Choose a password for the MySQL administrator (root). Keep it safe;' -ForegroundColor Yellow
        Write-Host 'Initialize-ZedDatabase.ps1 asks for it.' -ForegroundColor Yellow
        $rootPassword = Read-NewPassword 'MySQL root password'
        $sqlPassword = $rootPassword -replace '\\', '\\\\' -replace "'", "\'"
        "ALTER USER 'root'@'localhost' IDENTIFIED BY '$sqlPassword';" | & "$base\bin\mysql.exe" --user=root --skip-password --host=localhost
        if ($LASTEXITCODE -ne 0) { throw 'Setting the MySQL root password failed.' }
        Write-Ok "MySQL $MySqlVersion running as service $MySqlService (127.0.0.1:3306 only)"
    }

    Write-Host ''
    Write-Host 'Server prerequisites are installed.' -ForegroundColor Green
    if ($rebootNeeded) { Write-Warning 'Restart the server once before continuing (Windows asked for a restart).' }
    Write-Host 'Next: scripts\Initialize-ZedDatabase.ps1'
} finally {
    Stop-ZedLog
}
