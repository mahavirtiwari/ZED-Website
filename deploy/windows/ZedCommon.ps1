# Helpers shared by the ZED PowerShell scripts that run on your Windows PC
# (dot-sourced: . "$PSScriptRoot\ZedCommon.ps1"). Windows PowerShell 5.1 compatible.

$ErrorActionPreference = 'Stop'
$ProgressPreference = 'SilentlyContinue'
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

function Write-Step([string]$Text) { Write-Host "`n==> $Text" -ForegroundColor Cyan }
function Write-Ok([string]$Text) { Write-Host "    $Text" -ForegroundColor Green }

# Zips a folder with "/" in entry names (ZipFile.CreateFromDirectory in Windows
# PowerShell 5.1 writes "\", which Linux unzip tools do not understand).
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

# Converts Windows line endings to Unix ones (bash scripts on the server).
function ConvertTo-UnixFile([string]$Path) {
    $text = [IO.File]::ReadAllText($Path)
    if ($text.Contains("`r`n")) {
        [IO.File]::WriteAllText($Path, $text.Replace("`r`n", "`n"), (New-Object Text.UTF8Encoding $false))
    }
}
