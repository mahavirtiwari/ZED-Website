<#
.SYNOPSIS
    STEP 1 - on your PC (the Dell). Downloads or updates the ZED code from GitHub
    and builds the release package (a .zip) to copy to the staging server.

.DESCRIPTION
    - Clones https://github.com/mahavirtiwari/ZED-Website.git the first time,
      afterwards pulls the latest commits (Git asks you to sign in to GitHub
      once, because the repository is private).
    - Builds ZED-release-<date>-<commit>.zip in the packages folder with:
        app\       the Laravel website + CMS (Code\zed-laravel, without vendor\)
        assets\    the website theme files (Code\zedcms\assets)
        database\  zedcms.sql and the upgrade scripts
        scripts\   these deployment scripts
    No PHP is needed on this PC; the server installs the PHP libraries itself.

    First time on a new PC:
        git clone https://github.com/mahavirtiwari/ZED-Website.git C:\ZED\ZED-Website
        powershell -ExecutionPolicy Bypass -File C:\ZED\ZED-Website\deploy\windows\Get-ZedCode.ps1
    Run from inside a clone, it updates that clone and puts the zip in the
    "packages" folder next to it (C:\ZED\packages in this example).

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Get-ZedCode.ps1
.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Get-ZedCode.ps1 -WorkDir D:\ZED
#>
param(
    [string]$RepoUrl = 'https://github.com/mahavirtiwari/ZED-Website.git',
    [string]$Branch = 'main',
    [string]$WorkDir = (Join-Path $env:USERPROFILE 'ZED')
)

. "$PSScriptRoot\ZedCommon.ps1"

function Invoke-Git {
    & git @args
    if ($LASTEXITCODE -ne 0) { throw "git $($args -join ' ') failed (exit code $LASTEXITCODE)." }
}

function Copy-Tree([string]$From, [string]$To, [string[]]$ExcludeDirs = @(), [string[]]$ExcludeFiles = @()) {
    $arguments = @($From, $To, '/E', '/NFL', '/NDL', '/NJH', '/NJS', '/NP', '/R:2', '/W:2')
    if ($ExcludeDirs.Count) { $arguments += '/XD'; $arguments += $ExcludeDirs }
    if ($ExcludeFiles.Count) { $arguments += '/XF'; $arguments += $ExcludeFiles }
    & robocopy @arguments | Out-Null
    if ($LASTEXITCODE -ge 8) { throw "Copying $From failed (robocopy exit code $LASTEXITCODE)." }
}

# --- Git -----------------------------------------------------------------------
Write-Step 'Checking Git'
if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    if (Get-Command winget -ErrorAction SilentlyContinue) {
        Write-Host 'Git is not installed; installing it with winget...'
        winget install --id Git.Git -e --source winget --accept-package-agreements --accept-source-agreements
        $env:Path = [Environment]::GetEnvironmentVariable('Path', 'Machine') + ';' + [Environment]::GetEnvironmentVariable('Path', 'User')
    }
    if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
        throw 'Git is not installed. Install it from https://git-scm.com/download/win, then run this script again.'
    }
}
git --version

# --- Clone or update -------------------------------------------------------------
# Run from inside a clone (ZED-Website\deploy\windows): update that clone.
$scriptRepo = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
if (-not $PSBoundParameters.ContainsKey('WorkDir') -and (Test-Path (Join-Path $scriptRepo '.git'))) {
    $repo = $scriptRepo
    $WorkDir = Split-Path -Parent $repo
} else {
    $repo = Join-Path $WorkDir 'ZED-Website'
}
$packages = Join-Path $WorkDir 'packages'
New-Item -ItemType Directory -Force -Path $WorkDir, $packages | Out-Null

if (Test-Path (Join-Path $repo '.git')) {
    Write-Step "Updating $repo"
    Invoke-Git -C $repo fetch origin
    Invoke-Git -C $repo checkout $Branch
    Invoke-Git -C $repo pull --ff-only origin $Branch
} else {
    Write-Step "Downloading the code into $repo (sign in to GitHub if asked)"
    Invoke-Git clone --branch $Branch $RepoUrl $repo
}
$commit = (& git -C $repo rev-parse --short HEAD).Trim()
$commitText = (& git -C $repo log -1 --format='%h %ad %s' --date=short).Trim()
Write-Host "Latest commit: $commitText"

# --- Build the package ---------------------------------------------------------------
$stamp = Get-Date -Format 'yyyyMMdd-HHmm'
$name = "ZED-release-$stamp-$commit"
$stage = Join-Path $packages $name
if (Test-Path $stage) { Remove-Item -Recurse -Force $stage }
New-Item -ItemType Directory -Path $stage | Out-Null

Write-Step 'Collecting the website files'
$laravel = Join-Path $repo 'Code\zed-laravel'
Copy-Tree $laravel (Join-Path $stage 'app') `
    -ExcludeDirs @((Join-Path $laravel 'vendor'), (Join-Path $laravel 'node_modules'), (Join-Path $laravel 'tests'),
                   (Join-Path $laravel 'public\assets'), (Join-Path $laravel 'public\uploads'),
                   (Join-Path $laravel 'public\photogallery'), (Join-Path $laravel 'public\slider')) `
    -ExcludeFiles @('.env', '*.log')
# runtime folders start empty on the server
foreach ($dir in 'storage\framework\cache\data', 'storage\framework\sessions', 'storage\framework\views', 'storage\logs') {
    Get-ChildItem -Path (Join-Path $stage "app\$dir") -File -ErrorAction SilentlyContinue |
        Where-Object { $_.Name -ne '.gitignore' } | Remove-Item -Force
}
Get-ChildItem -Path (Join-Path $stage 'app\bootstrap\cache') -Filter *.php -ErrorAction SilentlyContinue | Remove-Item -Force

Copy-Tree (Join-Path $repo 'Code\zedcms\assets') (Join-Path $stage 'assets') -ExcludeFiles @('*.php')
Copy-Tree (Join-Path $repo 'DATABASE') (Join-Path $stage 'database')
Copy-Tree (Join-Path $repo 'deploy\windows') (Join-Path $stage 'scripts')

@"
ZED website release package
Built:  $(Get-Date -Format 'yyyy-MM-dd HH:mm')
Commit: $commitText
Source: $RepoUrl ($Branch)
"@ | Set-Content -Encoding UTF8 (Join-Path $stage 'RELEASE.txt')

$imageCount = (Get-ChildItem -Path (Join-Path $stage 'assets\images') -Recurse -File -Include *.png, *.jpg, *.jpeg, *.svg -ErrorAction SilentlyContinue).Count
if ($imageCount -lt 50) {
    Write-Warning "Only $imageCount theme images found in assets\images. The GitHub copy may be missing images; see the deployment guide, section 'Images'."
}

Write-Step 'Creating the zip file'
$zip = Join-Path $packages "$name.zip"
New-ZipFromFolder $stage $zip
Remove-Item -Recurse -Force $stage

$sizeMb = [math]::Round((Get-Item $zip).Length / 1MB, 1)
Write-Host ''
Write-Host "Package ready: $zip ($sizeMb MB)" -ForegroundColor Green
Write-Host 'Next: copy this zip to the staging server (for example C:\ZED-setup\) and run'
Write-Host '      scripts\Deploy-ZedSite.ps1 there (see the deployment guide).'
