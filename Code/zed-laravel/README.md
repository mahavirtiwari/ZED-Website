# ZED website on Laravel

Laravel 13 (PHP 8.4) version of the ZED website, made up of the public site and the CMS at `/cms`. It replaces the CodeIgniter 3 code in `Code/zedcms`. It uses the **same MySQL database** (`zedcms`) and the same uploaded files, so both versions can run side by side until the switch-over.

## What is where

| Part | Location |
|---|---|
| Public site controller | `app/Http/Controllers/SiteController.php` (ported from the old `Home` controller) |
| Public site queries | `app/Support/SiteData.php` (ported from `Home_model`) |
| Public site templates | `resources/views/public/*.blade.php`. These are the old templates converted to Blade with the **same markup and CSS**, so the site looks the same. The old CodeIgniter form helpers they still call are in `app/ci_helpers.php`. |
| Routes | `routes/site.php` (same URLs as before; `/{slug}` serves pages and news), `routes/cms.php` |
| CMS modules | `app/Cms/Resources/*Resource.php`, one class per module, listed in `config/cms.php` |
| CMS screens | Generic list and form screens: `app/Http/Controllers/Cms/ResourceController.php`, `resources/views/cms/resource/*` |
| CMS other screens | Sign-in, profile, users, settings, security, news blocks, contacts, dashboard: `app/Http/Controllers/Cms/*` |
| Models | `app/Models/*` on the existing tables. `Concerns/ZedContent` applies the status convention (2 = deleted, never removed) and fills `created_by` / `modified_by`. |
| Users and roles | `users`, `groups` and `users_groups` (the Ion Auth tables). Roles: superadmin, admin, contentmanager. The existing bcrypt passwords work unchanged. |

### Adding a CMS module

1. Create a model on the table.
2. Write a `Resource` class that declares its fields and list columns.
3. Add the class to `config/cms.php`.

Field types: text, url, email, number, textarea, editor, date, select, multiselect (ordered), toggle, image and file. Fields can be shown only for some values of another field with `showIf()`.

## What the CMS controls on the website

| Website part | CMS place |
|---|---|
| Menus, pages, news, FAQs, sliders, videos, initiatives, benefits, notifications, gallery, partners, testimonials, resources, certified MSMEs, guidance documents, parameters, sectors, disciplines | Their modules |
| Header logos and links, footer copyright, footer logo, social media links, text after page titles, default page description | Settings › Header and footer |
| Home page: top heading, text and two buttons, announcement label, the heading of every section, and a show/hide switch for each section | Settings › Home page |
| About-us text, SEO texts, counters (numbers, labels, show/hide), contact details and working hours | Settings › About us / Home page SEO / Gallery SEO / Counters / Contact details |
| Special content on a page: embedded web page (below or instead of the content, or full width), FAQ list, certificate verification form, maturity matrix, guidance documents, parameters | Pages › Special content (no longer tied to page addresses in the code) |
| Captcha switches, Contact Us form type | Settings › Security |

The defaults for every setting are in `config/cms.php`. They are what the site shows until a setting is saved.

## Security (audit "Verification Report No. I")

| Finding | Where it is handled |
|---|---|
| Security headers / CSP without `unsafe-inline` | `app/Http/Middleware/SecurityHeaders.php` sends a per-request nonce CSP with no `unsafe-inline` or `unsafe-eval`, plus `X-XSS-Protection: 1; mode=block` and the other hardening headers. `app/Support/HtmlHardener.php` (the `harden` middleware) adds nonces and turns the `style=""`, `on…=""` and `javascript:` in old templates and CMS content into CSP-safe code. **New views must not use inline styles or handlers.** |
| E-mail harvesting | On public pages `HtmlHardener` shows addresses as `abc[at]nic[dot]in` and drops `mailto:` links. |
| Captcha | `app/Support/Captcha.php`, image at `/captcha/{form}.png`. It is on the CMS sign-in, forgot password and the website Contact Us form. Switch it on or off in **CMS › Settings › Security**. |
| Clear-text password | `app/Support/PasswordTransport.php`, `app/Http/Middleware/DecryptPasswords.php` and `public/js/zed-pwt.js`. The browser encrypts passwords with RSA-OAEP using a per-session key and a single-use nonce. |
| Autocomplete on password fields | On CMS pages, `HtmlHardener` adds `autocomplete="off"` to forms and password fields. |
| Brute force | Sign-in is throttled (5 failures per address and IP, 15-minute lock). The captcha is checked before the password. |
| Uploads | Random file names; type and size are checked; images must be real images. Files are stored in `uploads/`, `photogallery/` and `slider/`, where the web server must never run scripts. |

The tests for these are in `tests/Feature/SecurityTest.php`: run `php artisan test`.

**Why not Filament.** Filament (with Livewire and Alpine) needs `unsafe-eval` and inline styles. That would reopen audit item 4, so the CMS is built from plain Blade pages and small scripts instead.

## Run locally (Windows)

Requirements: PHP 8.4 with `intl`, `gd`, `openssl` and `pdo_mysql`, and Composer. The MySQL started by `.localdev\start-local.ps1` must be running.

```powershell
cd Code\zed-laravel
php C:\Users\HP\tools\composer\composer.phar install   # or: composer install
copy .env.example .env                     # then set APP_ENV=local, APP_DEBUG=true, APP_URL=http://127.0.0.1:8090,
                                           # DB_USERNAME=root, ZED_STORAGE_ROOT=../zedcms, MAIL_MAILER=log
php artisan key:generate
php artisan migrate                        # additive only: users.remember_token, password_reset_tokens
powershell -ExecutionPolicy Bypass -File ..\..\.localdev\link-laravel-public.ps1   # shared assets and uploads
php artisan serve --port=8090
```

The site is then at http://127.0.0.1:8090 and the CMS at http://127.0.0.1:8090/cms. The old site keeps running on port 8080 against the same database.

## Deploy (Linux, nginx + PHP-FPM)

1. Copy the app to the server, for example `/var/www/zed-laravel`, and run `composer install --no-dev --optimize-autoloader`.
2. Set up `.env` from `.env.example`: database, SMTP, `APP_URL`, `ZED_VERIFY_TOKEN`. Then run `php artisan key:generate` and `php artisan migrate --force`.
3. Move (or symlink) `assets/`, `uploads/`, `photogallery/` and `slider/` from the old web root into `public/`.
4. Run `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
5. Make `storage/` and `bootstrap/cache/` writable by PHP-FPM. Enable opcache.
6. Use `deploy/nginx/zed-laravel.conf`, over HTTPS only.

## Deploy (Windows Server, IIS)

The staging site (zedcms.umon.in) runs on Windows Server with IIS, PHP 8.4 (FastCGI) and MySQL 8.4. PowerShell scripts in `deploy/windows` install everything and deploy each release; `public/web.config` holds the IIS rules. See `deploy/windows/README.md` and the step-by-step guide `deploy/windows/ZED-Staging-Deployment-Guide.docx`.

To create a CMS superadmin, or reset that user's password, on any server: `php artisan zed:cms-admin someone@example.com`.

### Switch-over

The old and new apps share the database. Content edited in either CMS shows on both sites, and settings changes appear within a minute.

1. Deploy the Laravel app on a staging hostname against a copy of the production database, and let editors try the CMS.
2. Deploy it to production. Point the vhost root at `zed-laravel/public` and move the upload folders as in step 3 above. All public URLs stay the same, and old CMS links such as `/cms/auth/login` redirect.
3. Keep the old code for a few weeks in case you need to roll back. Rolling back means pointing the vhost at the old web root again: the schema changes are only additive.

## Not done yet

- **Bootstrap 5 / no jQuery on the public site.** The public templates still use the old Bootstrap 3 markup, `style.css` and jQuery plugins. That keeps the look identical and makes page-by-page comparison possible. The JavaScript is the trimmed, audit-clean build. Restyling to Bootstrap 5 is the next step.
- The CMS uses Bootstrap 5.3. jQuery stays only for the Trumbowyg editor.
- Forgot-password e-mails need working SMTP settings (`MAIL_*`).
