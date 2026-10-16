# ZED website: upgrade notes and local setup

## Run it locally

Requirements, both already installed on this PC: PHP 8.4 on PATH, and MySQL Server 8.4 in `C:\Program Files\MySQL\MySQL Server 8.4`.

```powershell
powershell -ExecutionPolicy Bypass -File .localdev\start-local.ps1
```

- Public site: http://127.0.0.1:8080/
- CMS admin: http://127.0.0.1:8080/cms/. The local-only login is in `.localdev\local-admin.txt`. Run `php .localdev\seed-local-admin.php` to reset it.
- To stop: press Ctrl+C for the web server, and run `.localdev\stop-local.ps1` for MySQL.

On first run the script:
1. creates its own MySQL data folder (`.localdev\mysql-data`);
2. imports `DATABASE\zedcms.sql`, then `DATABASE\upgrade_mysql8.sql`;
3. seeds the local admin.

`.localdev\router.php` reproduces the Apache `.htaccess` rules for PHP's built-in server.

Images under `/uploads/...` that were uploaded on production are not part of this code copy. They show as broken locally until you copy the server's `/var/www/html/uploads` folder to `Code\zedcms\uploads`.

## What was upgraded

| Component | Before | After |
|---|---|---|
| PHP target | 7.x | 8.2 – 8.5 (tested on 8.4) |
| CodeIgniter (site + CMS) | 3.1.3 | 3.4.5 ([pocketarc/codeigniter](https://github.com/pocketarc/codeigniter)) |
| Database | MariaDB 10.4 | MySQL 8.4 (`DATABASE/upgrade_mysql8.sql`) |
| PHPMailer | 5.2.16 (broken, CVE-2016-10033) | 7.1.1 |
| jQuery (CMS) | 1.11.3 / 3.3.1 (CDN) | 3.7.1 + jQuery Migrate 3.5.2 |
| Bootstrap JS (site + CMS) | 3.3.7 | 3.4.1 |
| Bootstrap CSS (site) | 3.3.7 | 3.4.1, with the custom container widths re-applied |
| DataTables (CMS) | 1.10.12 | 1.13.11 |
| Trumbowyg editor (CMS) | 2.5.1 / 2.21.0 | 2.28.0 |
| Select2 (CMS) | 4.0.3 | 4.0.13 |
| Chart.js (CMS) | 2.3.0 | 2.9.4 |
| jQuery UI (CMS) | 1.12.1 | 1.13.3 |

Notes on the choices:
- **CodeIgniter 3.4.5.** Official CodeIgniter 3 stopped at 3.1.13 (2022) and is not clean on PHP 8.2+. 3.4.5 is the actively maintained fork; it is a drop-in replacement for CI3.
- **Not a rewrite.** Moving to CodeIgniter 4 or Bootstrap 5 would be a full rewrite of every controller and view, so it was left out.
- **CMS Bootstrap CSS.** It is a custom build from the old Bootstrap Customizer, so it was kept. All the Bootstrap 3.3.7 CVEs are in the JavaScript, and that was upgraded.

## Production deployment checklist

Set these environment variables in the web server or PHP-FPM pool:

| Variable | Purpose |
|---|---|
| `CI_ENV=production` | Hides PHP and SQL errors from visitors. The default is `development`. |
| `APP_BASE_URL` | e.g. `https://zed.msme.gov.in/`. Without it the URL is taken from the request's Host header. |
| `CMS_BASE_URL` | e.g. `https://zed.msme.gov.in/cms/` |
| `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` | Database connection |
| `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`, `SMTP_SECURE`, `MAIL_FROM`, `MAIL_FROM_NAME` | Contact-form mail |
| `ZED_VERIFY_TOKEN` | Token for the certificate-verification API |

Then:
1. Run `DATABASE/upgrade_mysql8.sql` once against the production database. **Take a backup first.**
2. On Apache, the new root `.htaccess` and the `.htaccess` files in `uploads/`, `photogallery/` and `slider/` (which block script execution) need `AllowOverride All`. On nginx:
   - for the site, use `try_files $uri $uri/ /index.php?$args;`;
   - for `/cms/`, use `try_files $uri $uri/ /cms/index.php?$args;`;
   - do not pass `uploads/`, `photogallery/` or `slider/` to PHP.
3. **Rotate these secrets.** They were committed in the old code and must be treated as leaked:
   - the production DB password (it was in a comment in `config/database.php`);
   - the Mandrill SMTP key;
   - the ZED verify-API token.
4. Restrict the Google Maps API key to your domains. The Universal Analytics ID `UA-99946113-1` is retired, so move to GA4 if analytics is still needed.
5. Check that no `admin@admin.com` (Ion Auth default) account exists.
6. Security audit fixes (October 2026): run `DATABASE/upgrade_2026_10_security.sql`, apply `deploy/nginx/zed.conf` (nginx) and serve over HTTPS only. See `SECURITY-AUDIT-RESPONSE.md` for the details and the CMS > Security settings.

## Laravel version (October 2026)

`Code/zed-laravel` is the Laravel 13 version of the public site and the CMS (Bootstrap 5.3 CMS, same database and uploads, same public URLs). It runs side by side with this CodeIgniter code until the switch-over. See `Code/zed-laravel/README.md` for local setup, deployment (`deploy/nginx/zed-laravel.conf`) and the switch-over steps.
