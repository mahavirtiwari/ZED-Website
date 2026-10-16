<#
.SYNOPSIS
    STEP 3 - on the staging server, once. Creates the zedcms database and its
    own MySQL user, imports the data and saves the connection settings in
    C:\inetpub\zedcms\shared\.env for the website.

.DESCRIPTION
    Imports, in this order:
      1. the dump: database\zedcms.sql from the package, or -DumpFile
         (use a fresh export of the production database for realistic staging)
      2. database\upgrade_mysql8.sql             (MariaDB 10.4 -> MySQL 8.4 changes)
      3. database\upgrade_2026_10_security.sql   (security audit settings)
    The website's own changes (php artisan migrate) run in Deploy-ZedSite.ps1.

    The database user "zed" gets a random password, written only to the
    website's .env file. You are asked for the MySQL root password.

.PARAMETER DumpFile
    A .sql export to import instead of database\zedcms.sql.
.PARAMETER SkipUpgradeScripts
    Use with a dump taken from a database that already had the upgrade scripts
    applied (for example a dump of another server that already runs this version).
.PARAMETER Replace
    Drop and re-import an existing zedcms database (asks for confirmation).

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Initialize-ZedDatabase.ps1
.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Initialize-ZedDatabase.ps1 -DumpFile C:\ZED-setup\zedcms-production.sql
#>
param(
    [string]$DumpFile,
    [switch]$SkipUpgradeScripts,
    [switch]$Replace,
    [string]$Database = 'zedcms',
    [string]$AppUser = 'zed',
    [string]$MySqlBin,
    [string]$SiteRoot = 'C:\inetpub\zedcms',
    [string]$HostName = 'zedcms.umon.in'
)

. "$PSScriptRoot\ZedCommon.ps1"
Assert-Admin
Start-ZedLog 'database'

$package = Split-Path -Parent $PSScriptRoot
$sqlDir = Join-Path $package 'database'
if (-not $DumpFile) { $DumpFile = Join-Path $sqlDir 'zedcms.sql' }
if (-not (Test-Path $DumpFile)) { throw "Database dump not found: $DumpFile" }
if (-not $MySqlBin) { $MySqlBin = Get-MySqlBin }
$mysql = Join-Path $MySqlBin 'mysql.exe'

# root credentials go in a temporary option file, not on the command line
$optionFile = Join-Path $env:TEMP ('zed-mysql-' + [guid]::NewGuid().ToString('N') + '.cnf')

function Invoke-MySql([string]$Sql, [string]$Db = '') {
    $arguments = @("--defaults-extra-file=$optionFile", '--batch', '--skip-column-names')
    if ($Db) { $arguments += $Db }
    $output = $Sql | & $mysql @arguments
    if ($LASTEXITCODE -ne 0) { throw "MySQL command failed: $Sql" }
    return $output
}

function Import-SqlFile([string]$File) {
    Write-Host "    importing $(Split-Path -Leaf $File)"
    # cmd handles the < redirection byte for byte (PowerShell would re-encode the file)
    cmd /c "`"$mysql`" --defaults-extra-file=`"$optionFile`" --default-character-set=utf8mb4 $Database < `"$File`""
    if ($LASTEXITCODE -ne 0) { throw "Importing $File failed." }
}

try {
    Write-Step 'MySQL administrator login'
    $rootPassword = ConvertTo-PlainText (Read-Host -AsSecureString 'MySQL root password')
    $escaped = $rootPassword -replace '\\', '\\\\' -replace '"', '\"'
    [IO.File]::WriteAllLines($optionFile, @('[client]', 'user=root', "password=`"$escaped`"", 'host=127.0.0.1', 'port=3306'), (New-Object Text.UTF8Encoding $false))
    Invoke-MySql 'SELECT VERSION();' | ForEach-Object { Write-Ok "connected to MySQL $_" }

    Write-Step "Database $Database"
    $exists = Invoke-MySql "SHOW DATABASES LIKE '$Database';"
    $import = $true
    if ($exists) {
        if ($Replace) {
            $answer = Read-Host "Type DELETE to drop the existing database $Database and import it again"
            if ($answer -ne 'DELETE') { throw 'Stopped; nothing was changed.' }
            Invoke-MySql "DROP DATABASE ``$Database``;"
        } else {
            Write-Ok "already exists; keeping its data (use -Replace to import again)"
            $import = $false
        }
    }
    if ($import) {
        Invoke-MySql "CREATE DATABASE ``$Database`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        Import-SqlFile $DumpFile
        if (-not $SkipUpgradeScripts) {
            Import-SqlFile (Join-Path $sqlDir 'upgrade_mysql8.sql')
            Import-SqlFile (Join-Path $sqlDir 'upgrade_2026_10_security.sql')
        }
        $tables = Invoke-MySql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$Database';"
        Write-Ok "imported ($tables tables)"
    }

    Write-Step "Database user $AppUser (used by the website)"
    $envFile = Initialize-SharedEnv $SiteRoot (Join-Path $package 'app\.env.example') $HostName
    # keep the password the website already uses (running this script again must not break the site)
    $appPassword = Get-EnvValue $envFile 'DB_PASSWORD'
    if (-not $appPassword -or (Get-EnvValue $envFile 'DB_USERNAME') -ne $AppUser) { $appPassword = New-RandomSecret 32 }
    foreach ($h in 'localhost', '127.0.0.1') {
        Invoke-MySql "CREATE USER IF NOT EXISTS '$AppUser'@'$h' IDENTIFIED BY '$appPassword';"
        Invoke-MySql "ALTER USER '$AppUser'@'$h' IDENTIFIED BY '$appPassword';"
        Invoke-MySql "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, REFERENCES, LOCK TABLES ON ``$Database``.* TO '$AppUser'@'$h';"
    }
    Invoke-MySql 'FLUSH PRIVILEGES;'
    Write-Ok "user $AppUser has access to $Database only"

    Write-Step 'Saving the connection settings for the website'
    Set-EnvValue $envFile 'DB_CONNECTION' 'mysql'
    Set-EnvValue $envFile 'DB_HOST' '127.0.0.1'
    Set-EnvValue $envFile 'DB_PORT' '3306'
    Set-EnvValue $envFile 'DB_DATABASE' $Database
    Set-EnvValue $envFile 'DB_USERNAME' $AppUser
    Set-EnvValue $envFile 'DB_PASSWORD' $appPassword
    Write-Ok "written to $envFile"

    Write-Host ''
    Write-Host 'Database ready.' -ForegroundColor Green
    Write-Host 'Next: scripts\Deploy-ZedSite.ps1'
} finally {
    if (Test-Path $optionFile) { Remove-Item -Force $optionFile }
    Stop-ZedLog
}
