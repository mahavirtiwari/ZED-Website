# ZED website (Laravel) — notes for coding agents

Read `README.md` first. Key rules for this codebase:

- **Strict CSP (security audit item 4).** Pages are served with a nonce-based Content-Security-Policy without `'unsafe-inline'` or `'unsafe-eval'`. Never add those keywords, inline `style=""`, `on…=""` handlers or `javascript:` links in new views or in HTML built by scripts; use classes and `addEventListener`. Inline `<script>`/`<style>` blocks get the nonce automatically (`HardenHtml` middleware). Do not add Livewire, Alpine or Filament (they need `unsafe-eval`).
- **Shared database.** The tables belong to the old CodeIgniter site too (`Code/zedcms`), which still runs until the switch-over. Schema changes must be additive. Status `2` means deleted; rows are never removed (`App\Models\Concerns\ZedContent`).
- **CMS modules** are `App\Cms\Resource` classes listed in `config/cms.php`.
- **Public templates** in `resources/views/public` were converted from the old site with the same markup; keep them comparable with the old pages until the Bootstrap 5 restyle.
- Composer is at `C:\Users\HP\tools\composer\composer.phar` on the developer PC. Run `php artisan test` after changes.
