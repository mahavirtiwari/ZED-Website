# ZED website: response to Verification Report No. I (09-10-2026)

Auditor: AKS IT Services (Mr. Neeraj Sharma). Asset: https://zed-staging.qci.org.in.
Code changes are in this repository. Some items also need a server change, marked
**Server**: they are listed in [Deployment steps](#deployment-steps).

| # | Finding (status in V-1) | What was done | Where |
|---|---|---|---|
| 1 | Reflected XSS (closed) | No change needed. | |
| 2 | Sensitive File Disclosure (open) | All `.DS_Store` files deleted. `application/`, `system/`, `phpmailer/`, all dot-files, dumps, logs and config files now answer **404** on Apache (`.htaccess`) and nginx (`deploy/nginx/zed.conf`). The staging server ignores `.htaccess` (it served `/application/.htaccess`), so it needs the nginx rules. **Server** | `Code/zedcms/.htaccess`, `cms/.htaccess`, `deploy/nginx/zed.conf` |
| 3 | Open Ports (open) | Not code. Firewall: only 80/443 public, SSH limited to office/VPN IPs, MySQL bound to 127.0.0.1, PHP-FPM on a unix socket. **Server** | `deploy/nginx/zed.conf` (notes at the end) |
| 4 | Lack of Security Headers (open): *X-XSS-Protection cannot be 0; remove unsafe-inline from CSP* | Every PHP response sends `X-XSS-Protection: 1; mode=block`, `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Permissions-Policy`, HSTS (on HTTPS), and a **Content-Security-Policy with a per-request nonce and no `unsafe-inline`** (no `unsafe-eval` either). Pages are rewritten on output so they still work: inline `<script>`/`<style>` get the nonce, `style=""` attributes become classes re-applied through the CSSOM, `onclick=""`-style handlers and `javascript:` links become event listeners. The X-Powered-By header is removed. Static files get the non-CSP headers from `.htaccess` / nginx. | `application/third_party/zed/Zed_security.php`, `application/core/MY_Output.php`, `cms/application/core/MY_Output.php` |
| 5 | Email Harvesting (open) | On every public page, e-mail addresses in text are shown as `abc[at]nic[dot]in`, and `mailto:` links are removed. This includes CMS content, the footer and the contact details. | `Zed_security::obfuscate_emails()` |
| 6 | Vulnerable and Outdated Components (open) | Retire.js (the tool in the report) now finds **0** vulnerable components in the code (it found 60+ before). Details below. | `assets/`, `cms/assets/` |
| 7 | Captcha not implemented (open) | New 6-character alphanumeric image captcha, checked on the server, single use, expires after 10 minutes. It is on the **CMS login** and **forgot password** pages and on the **Contact Us** website form. Each can be switched on or off in **CMS > Security**. | `application/third_party/zed/Zed_captcha.php`, `*/modules/captcha/`, `auth/views/_captcha.php`, `home/views/contact_form.php` |
| 8 | Cleartext Submission of Password (open) | Password fields are encrypted in the browser before sending, with RSA-OAEP (WebCrypto) and a per-session 2048-bit key. A single-use nonce stops a captured value from being replayed. Only `pwt1:<ciphertext>` is posted; the clear-text field is not sent. The server decrypts before Ion Auth runs, and stored passwords stay bcrypt hashes. This applies to the login, change password, reset password, profile, create user and edit user forms. **HTTPS is still required.** | `cms/application/libraries/Password_transport.php`, `cms/application/hooks/`, `cms/assets/js/zed-pwt.js` |
| 9 | Autocomplete Enabled on Password Field (open) | Every CMS form and password field has `autocomplete="off"`. It is added automatically to every CMS page, so new forms are covered too. | `Zed_security::rewrite_attributes()` |

## Item 6: components, before and after

| Component (where the report found it) | Before | Now |
|---|---|---|
| Chart.js (`cms/assets/js/Chart.min.js`) | 2.1.6 on staging (includes Moment 2.13) | 2.9.4 (already in the repository; staging was not updated). Also removed from the login page, which did not use it |
| DataTables (`cms/assets/plugins/datatables...`) | 1.10.12 | 1.13.11. The 1.10.12 folder is deleted |
| DataTables (public `assets/js`) | 1.10.18 | 1.13.11 |
| Bootstrap JS (CMS and public) | 3.3.7 on staging, 3.4.1 in the repository | Bootstrap 3 has no free release that fixes CVE-2024-6484 (carousel), CVE-2024-6485 (button) or CVE-2025-1647. A custom build now contains only the plugins the site uses (transition, alert, collapse, dropdown, modal, scrollspy, tab), so the vulnerable ones are gone. Version strings are removed from the JS and CSS, as the report's recommendation allows. The real fix is Bootstrap 5, which is part of the planned upgrade (`MIGRATION-PLAN-CI4.md`) |
| Select2 (public) | 4.0.3 | 4.0.13 |
| jQuery 1.11.3 / 1.12.x / 2.1.3, jQuery UI 1.11.4 / 1.12.1, Bootstrap 3.3.7 source | unused copies still on the server | deleted |
| Google Maps API and Universal Analytics on every public page | loaded, but no map was shown, and UA was shut down by Google in 2024 | removed (this also removes the Maps API key from the page source) |

## How to verify

- Headers: `curl -sI https://<host>/` and `curl -sI https://<host>/cms/auth/login` show the CSP with `'nonce-…'` and no `unsafe-inline`, plus `X-XSS-Protection: 1; mode=block`.
- Files: `https://<host>/application/.htaccess`, `/cms/assets/plugins/.DS_Store` and `/application/cache/.htaccess` return 404.
- Libraries: run the Retire.js browser extension on `/cms/auth/login` and the home page, or run `npx retire --path Code/zedcms`.
- Password: in the browser developer tools (Network tab), the login POST body shows `password=pwt1:…`, not the password.
- Captcha: the CMS login, forgot password and Contact Us (website form) pages each show a 6-character code. A wrong code is rejected, and each code works once.
- Email: the contact page shows `zed[dot]support[at]qcin[dot]org`.

## CMS > Security settings

| Setting | Values | Default |
|---|---|---|
| Captcha on CMS login | Enabled / Disabled | Enabled |
| Contact Us form | Zoho Desk form (posts to Zoho, uses Zoho's own captcha) / Website form (saved in CMS > Contacts and e-mailed) | Zoho Desk form (unchanged behaviour) |
| Captcha on Contact Us (website form) | Enabled / Disabled | Enabled |

Only super admins and admins can open this page. If a setting row is missing, the captcha stays on.

## Deployment steps

1. Back up the database, then run `DATABASE/upgrade_2026_10_security.sql`. It adds the three settings and is safe to run twice.
2. Deploy the code with deletions (for example `rsync -a --delete`). The old library folders and the `.DS_Store` files must disappear from the server, not only from the repository.
3. **nginx (staging looks like nginx):** apply `deploy/nginx/zed.conf`, adjusting `server_name`, `root`, the certificate and the PHP-FPM socket. Then run `nginx -t` and reload. **Apache:** `AllowOverride All` for the site folder, with `mod_rewrite` and `mod_headers` enabled.
4. Serve the site **only over HTTPS**. The password encryption uses WebCrypto, which browsers only allow on HTTPS pages; on plain HTTP the form falls back to normal posting.
5. Firewall: only 80/443 open to the internet (see the end of `deploy/nginx/zed.conf`).
6. Set `CI_ENV=production` (already in `UPGRADE-NOTES.md`).
7. PHP needs the `openssl` and `gd` extensions (both standard).

## Known limits

- **Runtime HTML.** The CSP blocks `style=""` and `on…=""` attributes in HTML that scripts insert after the page loads. The two cases in the site (tables on Guidance Document and ZED Maturity) were moved to CSS classes. In the CMS editor (Trumbowyg), content formatting is re-applied by `cms/assets/js/zed-editor-styles.js`, scoped to the editor only. New code should use classes and `.on()` handlers instead of inline attributes.
- **Bootstrap 3** is end-of-life. The trimmed build removes the affected plugins, but the long-term fix is the Bootstrap 5 migration.
- **Forgot password** in the CMS does not send e-mail (Ion Auth's `use_ci_email` is off), as before. It now has a captcha, but is still not linked from the login page.
