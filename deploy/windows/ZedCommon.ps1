# Helpers shared by the ZED server scripts (dot-sourced: . "$PSScriptRoot\ZedCommon.ps1").
# Windows PowerShell 5.1 compatible.

$ErrorActionPreference = 'Stop'
$ProgressPreference = 'SilentlyContinue'
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

# Default locations on the staging server
$ZedDefaults = @{
    SetupDir = 'C:\ZED-setup'
    SiteRoot = 'C:\inetpub\zedcms'
    PhpDir   = 'C:\PHP'
    MySqlDir = 'C:\MySQL'
    HostName = 'zedcms.umon.in'
    SiteName = 'zedcms'
    PoolName = 'zedcms'
}

function Write-Step([string]$Text) { Write-Host "`n==> $Text" -ForegroundColor Cyan }
function Write-Ok([string]$Text) { Write-Host "    $Text" -ForegroundColor Green }

function Assert-Admin {
    $principal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
    if (-not $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
        throw 'Run this script in PowerShell opened with "Run as administrator".'
    }
}

function Start-ZedLog([string]$Name) {
    $logs = Join-Path $ZedDefaults.SetupDir 'logs'
    New-Item -ItemType Directory -Force -Path $logs | Out-Null
    $file = Join-Path $logs ("$Name-" + (Get-Date -Format 'yyyyMMdd-HHmmss') + '.log')
    Start-Transcript -Path $file | Out-Null
    Write-Host "Log file: $file"
}

function Stop-ZedLog { try { Stop-Transcript | Out-Null } catch { } }

# Runs a program and stops the script when it fails.
function Invoke-Native([string]$FilePath, [string[]]$Arguments, [int[]]$OkCodes = @(0)) {
    & $FilePath @Arguments
    if ($OkCodes -notcontains $LASTEXITCODE) {
        throw "$FilePath $($Arguments -join ' ') failed (exit code $LASTEXITCODE)."
    }
}

function Get-Download([string]$Url, [string]$FileName) {
    $dir = Join-Path $ZedDefaults.SetupDir 'downloads'
    New-Item -ItemType Directory -Force -Path $dir | Out-Null
    $path = Join-Path $dir $FileName
    if (-not (Test-Path $path)) {
        Write-Host "    downloading $Url"
        Invoke-WebRequest -Uri $Url -OutFile "$path.part" -UseBasicParsing
        Move-Item "$path.part" $path
    }
    return $path
}

# Zips a folder with "/" in entry names (ZipFile.CreateFromDirectory in Windows
# PowerShell 5.1 writes "\", which some unzip tools do not understand).
function New-ZipFromFolder([string]$Folder, [string]$Zip) {
    Add-Type -AssemblyName System.IO.Compression, System.IO.Compression.FileSystem
    if (Test-Path $Zip) { Remove-Item -Force $Zip }
    $root = (Resolve-Path $Folder).Path.TrimEnd('\') + '\'
    $archive = [IO.Compression.ZipFile]::Open($Zip, [IO.Compression.ZipArchiveMode]::Create)
    try {
        foreach ($file in [IO.Directory]::EnumerateFiles($root, '*', [IO.SearchOption]::AllDirectories)) {
            $entry = $file.Substring($root.Length).Replace('\', '/')
            [IO.Compression.ZipFileExtensions]::CreateEntryFromFile($archive, $file, $entry, [IO.Compression.CompressionLevel]::Optimal) | Out-Null
        }
    } finally {
        $archive.Dispose()
    }
}

function Expand-Zip([string]$Zip, [string]$Destination) {
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    if (Test-Path $Destination) { Remove-Item -Recurse -Force $Destination }
    [IO.Compression.ZipFile]::ExtractToDirectory($Zip, $Destination)
}

function Add-ToMachinePath([string]$Dir) {
    $path = [Environment]::GetEnvironmentVariable('Path', 'Machine')
    if (($path -split ';') -notcontains $Dir) {
        [Environment]::SetEnvironmentVariable('Path', ($path.TrimEnd(';') + ';' + $Dir), 'Machine')
    }
    if (($env:Path -split ';') -notcontains $Dir) { $env:Path = $env:Path.TrimEnd(';') + ';' + $Dir }
}

function Get-PhpExe {
    $php = Join-Path $ZedDefaults.PhpDir 'php.exe'
    if (Test-Path $php) { return $php }
    $cmd = Get-Command php -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }
    throw 'PHP not found. Run Install-ZedServer.ps1 first.'
}

function Get-MySqlBin {
    $saved = [Environment]::GetEnvironmentVariable('ZED_MYSQL_BIN', 'Machine')
    if ($saved -and (Test-Path (Join-Path $saved 'mysql.exe'))) { return $saved }
    $cmd = Get-Command mysql -ErrorAction SilentlyContinue
    if ($cmd) { return (Split-Path $cmd.Source) }
    foreach ($dir in @('C:\Program Files\MySQL\MySQL Server 8.4\bin', 'C:\Program Files\MySQL\MySQL Server 8.0\bin')) {
        if (Test-Path (Join-Path $dir 'mysql.exe')) { return $dir }
    }
    throw 'MySQL client (mysql.exe) not found. Run Install-ZedServer.ps1 first, or pass -MySqlBin.'
}

function ConvertTo-PlainText([Security.SecureString]$Secure) {
    $ptr = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($Secure)
    try { return [Runtime.InteropServices.Marshal]::PtrToStringBSTR($ptr) }
    finally { [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($ptr) }
}

function Read-NewPassword([string]$Prompt) {
    while ($true) {
        $first = ConvertTo-PlainText (Read-Host -AsSecureString $Prompt)
        $second = ConvertTo-PlainText (Read-Host -AsSecureString 'Type it again')
        if ($first -ne $second) { Write-Warning 'The two entries do not match. Try again.'; continue }
        if ($first.Length -lt 10) { Write-Warning 'Use at least 10 characters.'; continue }
        return $first
    }
}

# Random letters and digits (safe in .env files, SQL and URLs)
function New-RandomSecret([int]$Length = 32) {
    $chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'.ToCharArray()
    $bytes = New-Object byte[] $Length
    [Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
    return -join ($bytes | ForEach-Object { $chars[$_ % $chars.Length] })
}

function New-LaravelKey {
    $bytes = New-Object byte[] 32
    [Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
    return 'base64:' + [Convert]::ToBase64String($bytes)
}

# --- .env files (UTF-8 without BOM; a BOM would break the first setting) ------
function Read-EnvLines([string]$File) {
    if (-not (Test-Path $File)) { return @() }
    return [IO.File]::ReadAllLines($File)
}

function Get-EnvValue([string]$File, [string]$Key) {
    foreach ($line in (Read-EnvLines $File)) {
        if ($line -match "^\s*$([regex]::Escape($Key))\s*=(.*)$") {
            $value = $Matches[1].Trim()
            if ($value -match '^"(.*)"$' -or $value -match "^'(.*)'$") { $value = $Matches[1] }
            return $value
        }
    }
    return $null
}

function Set-EnvValue([string]$File, [string]$Key, [string]$Value) {
    if ($Value -match '[\s#"''\\$=]') {
        if ($Value -notmatch "'") { $Value = "'" + $Value + "'" }
        else { $Value = '"' + ($Value -replace '\\', '\\' -replace '"', '\"') + '"' }
    }
    $lines = New-Object Collections.Generic.List[string]
    $found = $false
    foreach ($line in (Read-EnvLines $File)) {
        if (-not $found -and $line -match "^\s*#?\s*$([regex]::Escape($Key))\s*=") {
            $lines.Add("$Key=$Value"); $found = $true
        } else {
            $lines.Add($line)
        }
    }
    if (-not $found) { $lines.Add("$Key=$Value") }
    [IO.File]::WriteAllLines($File, $lines, (New-Object Text.UTF8Encoding $false))
}

# The site's settings file lives outside the releases: <SiteRoot>\shared\.env
function Initialize-SharedEnv([string]$SiteRoot, [string]$Template, [string]$HostName) {
    $shared = Join-Path $SiteRoot 'shared'
    New-Item -ItemType Directory -Force -Path $shared | Out-Null
    $envFile = Join-Path $shared '.env'
    if (-not (Test-Path $envFile)) {
        if (-not (Test-Path $Template)) { throw "Template $Template not found." }
        [IO.File]::WriteAllLines($envFile, [IO.File]::ReadAllLines($Template), (New-Object Text.UTF8Encoding $false))
        Set-EnvValue $envFile 'APP_ENV' 'staging'
        Set-EnvValue $envFile 'APP_DEBUG' 'false'
        Set-EnvValue $envFile 'APP_URL' "http://$HostName"
        Set-EnvValue $envFile 'APP_KEY' (New-LaravelKey)
        Set-EnvValue $envFile 'LOG_LEVEL' 'warning'
        # cookies only over HTTPS once Enable-ZedHttps.ps1 has run
        Set-EnvValue $envFile 'SESSION_SECURE_COOKIE' 'false'
        # staging: e-mails are written to storage\logs\laravel.log instead of being sent
        Set-EnvValue $envFile 'MAIL_MAILER' 'log'
        Set-EnvValue $envFile 'ZED_STORAGE_ROOT' ''
        Write-Ok "created $envFile"
    }
    return $envFile
}

# Removes a junction or symbolic link without touching the folder it points to.
function Remove-Link([string]$Path) {
    if (-not (Test-Path -LiteralPath $Path)) { return }
    $item = Get-Item -LiteralPath $Path -Force
    if ($item.Attributes -band [IO.FileAttributes]::ReparsePoint) {
        if ($item.PSIsContainer) { cmd /c rmdir "`"$Path`"" | Out-Null }
        else { [IO.File]::Delete($Path) }
    }
}

function New-Junction([string]$Link, [string]$Target) {
    Remove-Link $Link
    if (Test-Path -LiteralPath $Link) { throw "$Link exists and is not a link." }
    New-Item -ItemType Junction -Path $Link -Target $Target | Out-Null
}
