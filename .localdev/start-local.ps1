# Starts the ZED website locally: MySQL 8.4 on 127.0.0.1:3306 and the PHP 8.4
# built-in server on http://127.0.0.1:8080 (public site) and /cms (admin).
#
#   powershell -ExecutionPolicy Bypass -File .localdev\start-local.ps1
#
# First run: initialises the MySQL data folder and imports DATABASE\zedcms.sql
# followed by DATABASE\upgrade_mysql8.sql and DATABASE\upgrade_2026_10_security.sql.

$ErrorActionPreference = 'Stop'
$root    = Split-Path -Parent $PSScriptRoot
$dev     = $PSScriptRoot
$mysqlBin = 'C:\Program Files\MySQL\MySQL Server 8.4\bin'
$dataDir = Join-Path $dev 'mysql-data'
$myIni   = Join-Path $dev 'my.ini'
$port    = 8080

if (-not (Get-Command php -ErrorAction SilentlyContinue)) { throw 'php is not on PATH (PHP 8.2+ required).' }
if (-not (Test-Path "$mysqlBin\mysqld.exe")) { throw "MySQL 8.4 not found in $mysqlBin" }

# my.ini with paths for this checkout
@"
[mysqld]
datadir=$($dataDir -replace '\\','/')
port=3306
bind-address=127.0.0.1
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
max_allowed_packet=64M
log-error=$((Join-Path $dev 'mysql-error.log') -replace '\\','/')
"@ | Set-Content -Encoding ascii $myIni

$fresh = -not (Test-Path $dataDir)
if ($fresh) {
    Write-Host 'Initialising MySQL data directory...'
    & "$mysqlBin\mysqld.exe" --initialize-insecure "--datadir=$dataDir" | Out-Null
}

$listening = Get-NetTCPConnection -LocalPort 3306 -State Listen -ErrorAction SilentlyContinue
if (-not $listening) {
    Write-Host 'Starting MySQL...'
    Start-Process -WindowStyle Hidden -FilePath "$mysqlBin\mysqld.exe" -ArgumentList "--defaults-file=`"$myIni`""
    for ($i = 0; $i -lt 30; $i++) {
        & "$mysqlBin\mysqladmin.exe" --user=root --host=127.0.0.1 ping 2>$null | Out-Null
        if ($LASTEXITCODE -eq 0) { break }
        Start-Sleep -Seconds 1
    }
}

$hasDb = & "$mysqlBin\mysql.exe" --user=root --host=127.0.0.1 -N -e "SHOW DATABASES LIKE 'zedcms'"
if (-not $hasDb) {
    Write-Host 'Importing database...'
    & "$mysqlBin\mysql.exe" --user=root --host=127.0.0.1 -e "CREATE DATABASE zedcms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    cmd /c "`"$mysqlBin\mysql.exe`" --user=root --host=127.0.0.1 zedcms < `"$root\DATABASE\zedcms.sql`""
    cmd /c "`"$mysqlBin\mysql.exe`" --user=root --host=127.0.0.1 zedcms < `"$root\DATABASE\upgrade_mysql8.sql`""
    cmd /c "`"$mysqlBin\mysql.exe`" --user=root --host=127.0.0.1 zedcms < `"$root\DATABASE\upgrade_2026_10_security.sql`""
    php "$dev\seed-local-admin.php"
}

Write-Host ""
Write-Host "Public site: http://127.0.0.1:$port/"
Write-Host "CMS admin:   http://127.0.0.1:$port/cms/  (login in .localdev\local-admin.txt)"
Write-Host 'Press Ctrl+C to stop the web server (run stop-local.ps1 to stop MySQL).'
Set-Location $root
php -S "127.0.0.1:$port" -t "Code\zedcms" "$dev\router.php"
