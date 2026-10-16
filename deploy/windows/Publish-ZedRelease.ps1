<#
.SYNOPSIS
    STEP 2 - on your PC (the Dell). Uploads the release package to the Ubuntu
    staging server over SSH and installs it there - no need to log in to the
    server yourself.

.DESCRIPTION
    Uses the SSH client built into Windows 10/11 (ssh.exe / scp.exe).
    The first time, add -Setup: the server then also gets nginx, PHP 8.4,
    Composer, MySQL and certbot (install-server.sh) and the database
    (init-database.sh). Every later release needs only this script without -Setup.

    On the server everything happens in /var/www/zedcms (see deploy/linux).
    Uploaded packages are kept in ~/zed-deploy on the server (last 3).
    You may be asked for passwords:
      - "[sudo] password for ubuntu" if your server account needs one (AWS: usually not)
      - the new CMS administrator password, when you use -AdminEmail

.PARAMETER Server
    Server address. Default 13.232.227.197 (the staging server).
.PARAMETER User
    SSH user. Default "ubuntu" (AWS Ubuntu servers).
.PARAMETER KeyFile
    The SSH private key (.pem) for the server, e.g. C:\ZED\keys\zed-staging.pem.
.PARAMETER Package
    The ZED-release-*.zip to install. Default: the newest one in the packages folder.
.PARAMETER Setup
    First installation: server software + database, then the website.
.PARAMETER DumpFile
    With -Setup: a production database export (.sql or .sql.gz) instead of the copy in the package.
.PARAMETER MediaZip
    ZED-media-*.zip from Export-ZedMedia.ps1 (uploaded images, slider, gallery photos).
.PARAMETER AdminEmail
    Create (or reset the password of) this CMS superadmin; you type the password.
.PARAMETER HttpsEmail
    Get the free Let's Encrypt SSL certificate and switch the site to HTTPS
    (when it is not on yet). Needs DNS and port 80, see the guide.
.PARAMETER Rollback
    Go back to the previous release on the server.
.PARAMETER CertificateStatus
    Show the SSL certificate's expiry date and automatic renewal.

.EXAMPLE
    # first time
    .\Publish-ZedRelease.ps1 -KeyFile C:\ZED\keys\zed-staging.pem -Setup -MediaZip C:\ZED\packages\ZED-media-20261016-1500.zip -AdminEmail you@example.com -HttpsEmail you@example.com
.EXAMPLE
    # every new release
    .\Publish-ZedRelease.ps1 -KeyFile C:\ZED\keys\zed-staging.pem
.EXAMPLE
    .\Publish-ZedRelease.ps1 -KeyFile C:\ZED\keys\zed-staging.pem -Rollback
#>
param(
    [string]$Server = '13.232.227.197',
    [string]$User = 'ubuntu',
    [string]$KeyFile,
    [int]$Port = 22,
    [string]$Package,
    [switch]$Setup,
    [string]$DumpFile,
    [string]$MediaZip,
    [string]$AdminEmail,
    [string]$HttpsEmail,
    [switch]$Rollback,
    [switch]$CertificateStatus,
    [string]$PackagesDir
)

. "$PSScriptRoot\ZedCommon.ps1"

# --- SSH client -------------------------------------------------------------------
$ssh = Get-Command ssh.exe -ErrorAction SilentlyContinue
$scp = Get-Command scp.exe -ErrorAction SilentlyContinue
if (-not $ssh -or -not $scp) {
    throw 'The Windows SSH client is missing. In PowerShell as administrator run: Add-WindowsCapability -Online -Name OpenSSH.Client~~~~0.0.1.0'
}

$sshArgs = @('-p', "$Port", '-o', 'StrictHostKeyChecking=accept-new', '-o', 'ServerAliveInterval=30')
$scpArgs = @('-P', "$Port", '-o', 'StrictHostKeyChecking=accept-new')
if ($KeyFile) {
    if (-not (Test-Path $KeyFile)) { throw "Key file not found: $KeyFile" }
    $KeyFile = (Resolve-Path $KeyFile).Path
    # ssh refuses keys that other Windows users can read
    & icacls.exe $KeyFile /inheritance:r /grant:r "${env:USERNAME}:R" | Out-Null
    $sshArgs += @('-i', $KeyFile)
    $scpArgs += @('-i', $KeyFile)
}
$target = "$User@$Server"

function Invoke-Remote([string]$Command, [switch]$Interactive) {
    $arguments = @($sshArgs)
    if ($Interactive) { $arguments += '-t' }
    $arguments += @($target, $Command)
    & $ssh.Source @arguments
    if ($LASTEXITCODE -ne 0) { throw "The command on the server failed (exit code $LASTEXITCODE). See the messages above." }
}

function Send-File([string]$Path) {
    Write-Host "    uploading $(Split-Path -Leaf $Path) ($([math]::Round((Get-Item $Path).Length / 1MB, 1)) MB)"
    & $scp.Source @scpArgs $Path "${target}:zed-deploy/"
    if ($LASTEXITCODE -ne 0) { throw "Uploading $Path failed." }
}

function Assert-Email([string]$Value, [string]$Name) {
    if ($Value -and $Value -notmatch '^[^@\s''"`$;&|<>]+@[^@\s''"`$;&|<>]+\.[A-Za-z]{2,}$') { throw "$Name is not a valid e-mail address: $Value" }
}
Assert-Email $AdminEmail '-AdminEmail'
Assert-Email $HttpsEmail '-HttpsEmail'

Write-Step "Connecting to $target"
# (no double quotes in remote commands: Windows PowerShell 5.1 does not pass them on to ssh.exe correctly)
Invoke-Remote 'mkdir -p ~/zed-deploy && echo connected to $(hostname), $(. /etc/os-release; echo $PRETTY_NAME)'

# the newest uploaded package's scripts (for -Rollback / -CertificateStatus)
$latestRemote = 'd=$(ls -d ~/zed-deploy/ZED-release-*/ 2>/dev/null | sort | tail -n 1); [ -z ${d:+x} ] && { echo No package on the server yet.; exit 1; }'
if ($Rollback) {
    Invoke-Remote "$latestRemote; sudo bash `${d}server/deploy.sh --rollback" -Interactive
    return
}
if ($CertificateStatus) {
    Invoke-Remote "$latestRemote; sudo bash `${d}server/enable-https.sh --status" -Interactive
    return
}

# --- Package -------------------------------------------------------------------------
if (-not $Package) {
    if (-not $PackagesDir) { $PackagesDir = Join-Path (Split-Path -Parent (Split-Path -Parent (Split-Path -Parent $PSScriptRoot))) 'packages' }
    $Package = Get-ChildItem -Path $PackagesDir -Filter 'ZED-release-*.zip' -ErrorAction SilentlyContinue |
        Sort-Object LastWriteTime -Descending | Select-Object -First 1 -ExpandProperty FullName
    if (-not $Package) { throw "No ZED-release-*.zip in $PackagesDir. Run Get-ZedCode.ps1 first, or pass -Package." }
}
if (-not (Test-Path $Package)) { throw "Package not found: $Package" }
$name = [IO.Path]::GetFileNameWithoutExtension($Package)
if ($name -notmatch '^ZED-release-[\w-]+$') { throw "Unexpected package name: $name" }
Write-Host "Package: $Package"

foreach ($file in @($MediaZip, $DumpFile)) {
    if ($file -and (Split-Path -Leaf $file) -match '[^\w.\-]') { throw "Rename $file to use only letters, digits, - and . (it is used in a command on the server)." }
}

Write-Step 'Uploading'
Send-File $Package
$deployArgs = ''
if ($MediaZip) {
    if (-not (Test-Path $MediaZip)) { throw "Media package not found: $MediaZip" }
    Send-File $MediaZip
    $deployArgs += " --media ~/zed-deploy/$(Split-Path -Leaf $MediaZip)"
}
$dumpArg = ''
if ($DumpFile) {
    if (-not $Setup) { throw '-DumpFile is used with -Setup only.' }
    if (-not (Test-Path $DumpFile)) { throw "Database export not found: $DumpFile" }
    Send-File $DumpFile
    $dumpArg = " --dump ~/zed-deploy/$(Split-Path -Leaf $DumpFile)"
}
if ($AdminEmail) { $deployArgs += " --admin-email '$AdminEmail'" }
if ($HttpsEmail) { $deployArgs += " --https-email '$HttpsEmail'" }

Write-Step 'Unpacking on the server'
Invoke-Remote ("cd ~/zed-deploy && rm -rf '$name' && python3 -m zipfile -e '$name.zip' '$name' && sed -i 's/\r$//' '$name'/server/*.sh && " +
               "ls -d ZED-release-*/ | sort | head -n -3 | xargs -r rm -rf && ls ZED-release-*.zip | sort | head -n -3 | xargs -r rm -f && echo '    done'")

$dir = "~/zed-deploy/$name/server"
if ($Setup) {
    Write-Step 'Installing the server software (first time, 5-15 minutes)'
    Invoke-Remote "sudo bash $dir/install-server.sh" -Interactive
    Write-Step 'Creating the database'
    Invoke-Remote "sudo bash $dir/init-database.sh$dumpArg" -Interactive
}

Write-Step 'Installing the website'
Invoke-Remote "sudo bash $dir/deploy.sh$deployArgs" -Interactive

Write-Host ''
Write-Host 'Done. Open the website in your browser to check it (see the guide, step B6).' -ForegroundColor Green
