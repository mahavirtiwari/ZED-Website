# Writes the Windows trusted root certificates to .localdev\windows-ca.pem, for
# PHP on this PC (it has no CA bundle, and antivirus HTTPS inspection adds its
# own root). The Laravel site uses it for the ZED portal APIs when
# Code\zed-laravel\.env has ZED_HTTP_CA_FILE=../../.localdev/windows-ca.pem
# Local development only; servers use the system CA bundle.
$out = Join-Path $PSScriptRoot 'windows-ca.pem'
$certs = @(Get-ChildItem Cert:\LocalMachine\Root) + @(Get-ChildItem Cert:\CurrentUser\Root) + @(Get-ChildItem Cert:\LocalMachine\CA)
$sb = New-Object System.Text.StringBuilder
$seen = @{}
foreach ($c in $certs) {
    if ($seen.ContainsKey($c.Thumbprint)) { continue }
    $seen[$c.Thumbprint] = 1
    [void]$sb.AppendLine('-----BEGIN CERTIFICATE-----')
    [void]$sb.AppendLine([Convert]::ToBase64String($c.RawData, 'InsertLineBreaks'))
    [void]$sb.AppendLine('-----END CERTIFICATE-----')
}
[IO.File]::WriteAllText($out, $sb.ToString().Replace("`r`n", "`n"))
"$($seen.Count) certificates written to $out"
