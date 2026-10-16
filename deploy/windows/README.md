# Deploying the ZED website to the Ubuntu staging server

Step-by-step guide: `ZED-Staging-Deployment-Guide.docx` in this folder.
Staging: zedcms.umon.in on Ubuntu (AWS, 13.232.227.197): nginx + PHP 8.4-FPM + MySQL,
free Let's Encrypt SSL certificate (certbot, renewed automatically).

Everything is started from your Windows PC with PowerShell; the scripts in
`deploy/linux` do the work on the server over SSH.

| Script | Where | When |
| --- | --- | --- |
| `Get-ZedCode.ps1` | your PC | every release: gets the code from GitHub, builds `ZED-release-*.zip` |
| `Publish-ZedRelease.ps1` | your PC | every release: uploads the zip and installs it (`-Setup` the first time, `-Rollback`, `-CertificateStatus`) |
| `Export-ZedMedia.ps1` | your PC | when needed: packs `uploads`, `slider`, `photogallery` into `ZED-media-*.zip` |
| `../linux/install-server.sh` | server (run by Publish) | once: nginx, PHP 8.4-FPM pool, Composer, MySQL, certbot |
| `../linux/init-database.sh` | server (run by Publish) | once: database, its user, import, connection settings |
| `../linux/deploy.sh` | server (run by Publish) | every release; `--rollback` |
| `../linux/enable-https.sh` | server (run by Publish) | once: free SSL certificate; `--status` |

The server already hosts other websites: the scripts only add the ZED site
(own nginx server block, own PHP-FPM pool and Linux user `zedcms`) and do not
change other sites or the firewall.

Layout on the server:

```
/var/www/zedcms/current        -> releases/<date-time>   (nginx root: current/public)
/var/www/zedcms/releases/      the last 3 releases
/var/www/zedcms/shared/.env    settings (database, APP_KEY, mail ...)
/var/www/zedcms/shared/storage logs, sessions, cache
/var/www/zedcms/shared/media   uploads, slider, photogallery
/etc/nginx/sites-available/zedcms.conf, /etc/php/8.4/fpm/pool.d/zedcms.conf
/var/log/zed-deploy/           script logs
```
