# Deploying the ZED website to a Windows server (IIS)

Step-by-step guide: `ZED-Staging-Deployment-Guide.docx` in this folder.
Staging: http(s)://zedcms.umon.in (Windows Server on Azure, IIS + PHP 8.4 + MySQL 8.4).

| Script | Where | When |
| --- | --- | --- |
| `Get-ZedCode.ps1` | your PC | every release: gets the code from GitHub and builds `ZED-release-*.zip` |
| `Export-ZedMedia.ps1` | where the uploaded files are | when needed: packs `uploads`, `slider`, `photogallery` into `ZED-media-*.zip` |
| `Install-ZedServer.ps1` | server, as administrator | once: IIS, URL Rewrite, VC++ runtime, PHP 8.4, Composer, MySQL 8.4 |
| `Initialize-ZedDatabase.ps1` | server, as administrator | once: database, its user, import, connection settings |
| `Deploy-ZedSite.ps1` | server, as administrator | every release (and `-Rollback`) |
| `Enable-ZedHttps.ps1` | server, as administrator | once: Let's Encrypt certificate and HTTPS everywhere |
| `ZedCommon.ps1` | (used by the others) | |

Layout on the server:

```
C:\inetpub\zedcms\
  current\            -> link to the live release (IIS site path: current\public)
  releases\<date>\    the last 3 releases
  shared\.env         settings (database, APP_KEY, mail ...)
  shared\storage\     logs, sessions, cache
  shared\media\       uploads, slider, photogallery (files added through the CMS)
C:\PHP\  C:\MySQL\  C:\ZED-setup\ (downloads, logs)
```
