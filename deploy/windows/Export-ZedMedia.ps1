<#
.SYNOPSIS
    Packs the uploaded content (uploads\, slider\, photogallery\) of an existing
    ZED web root into ZED-media-<date>.zip, for Deploy-ZedSite.ps1 -MediaZip.

.DESCRIPTION
    These folders hold what editors uploaded through the CMS (images, PDFs,
    slider and gallery photos). They are not in GitHub, so copy them from the
    site that has them:
      - a Windows copy of the production web root (folder that contains
        uploads\, slider\ and photogallery\), or
      - the developer PC: <checkout>\Code\zedcms
    On a Linux production server, make the same zip there instead, e.g.
      cd /var/www/zed && zip -r ~/ZED-media.zip uploads slider photogallery -x '*.php'
    PHP files are left out (uploads must never contain scripts).

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\Export-ZedMedia.ps1 -WebRoot 'D:\ZED Website code\Code\zedcms'
#>
param(
    [Parameter(Mandatory = $true)][string]$WebRoot,
    [string]$OutDir = (Join-Path $env:USERPROFILE 'ZED\packages')
)

. "$PSScriptRoot\ZedCommon.ps1"

$folders = 'uploads', 'slider', 'photogallery'
$found = $folders | Where-Object { Test-Path (Join-Path $WebRoot $_) }
if (-not $found) { throw "None of $($folders -join ', ') found in $WebRoot." }

New-Item -ItemType Directory -Force -Path $OutDir | Out-Null
$stage = Join-Path $OutDir ('ZED-media-' + (Get-Date -Format 'yyyyMMdd-HHmm'))
New-Item -ItemType Directory -Force -Path $stage | Out-Null

foreach ($folder in $found) {
    & robocopy (Join-Path $WebRoot $folder) (Join-Path $stage $folder) /E /NFL /NDL /NJH /NJS /NP /R:2 /W:2 /XF *.php *.phtml *.phar | Out-Null
    if ($LASTEXITCODE -ge 8) { throw "Copying $folder failed (robocopy exit code $LASTEXITCODE)." }
    $count = (Get-ChildItem -Recurse -File (Join-Path $stage $folder)).Count
    Write-Host "$folder : $count files"
}

$zip = "$stage.zip"
New-ZipFromFolder $stage $zip
Remove-Item -Recurse -Force $stage
Write-Host "Media package ready: $zip ($([math]::Round((Get-Item $zip).Length / 1MB, 1)) MB)" -ForegroundColor Green
