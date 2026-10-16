# Links the shared folders of the old site into the Laravel app's public/ folder
# (Windows directory junctions, no admin rights needed), so both apps serve the
# same assets and uploaded files during the switch-over:
#   Code\zed-laravel\public\{assets,uploads,photogallery,slider} -> Code\zedcms\...
#
#   powershell -ExecutionPolicy Bypass -File .localdev\link-laravel-public.ps1
#
# On a Linux server use symlinks instead (see Code\zed-laravel\README-ZED.md).

$ErrorActionPreference = 'Stop'
$root   = Split-Path -Parent $PSScriptRoot
$legacy = Join-Path $root 'Code\zedcms'
$public = Join-Path $root 'Code\zed-laravel\public'

foreach ($name in 'assets', 'uploads', 'photogallery', 'slider') {
    $link   = Join-Path $public $name
    $target = Join-Path $legacy $name
    if (Test-Path $link) {
        Write-Host "exists: $link"
        continue
    }
    New-Item -ItemType Junction -Path $link -Target $target | Out-Null
    Write-Host "linked: $link -> $target"
}
