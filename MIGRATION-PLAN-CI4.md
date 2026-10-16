# ZED website: migration plan to CodeIgniter 4 and Bootstrap 5

Status: **plan only, no code changed.** Written 2026-10-16 from a read of the code in `Code/zedcms` and of `DATABASE/zedcms.sql`, after the in-place upgrade described in `UPGRADE-NOTES.md`. That upgrade left the site on PHP 8.4, CodeIgniter 3.4.5 (pocketarc fork), HMVC (MX) and Ion Auth 2, Bootstrap 3.4.1 and MySQL 8.4.

Contents

1. [Summary](#1-summary)
2. [Target architecture](#2-target-architecture)
3. [Inventory and CI4 mapping](#3-inventory-and-ci4-mapping)
4. [CI3 to CI4 differences that touch this code](#4-ci3-to-ci4-differences-that-touch-this-code)
5. [Ion Auth to Shield](#5-ion-auth-to-shield)
6. [Bootstrap 3 to Bootstrap 5](#6-bootstrap-3-to-bootstrap-5)
7. [Risky areas](#7-risky-areas)
8. [Phased order](#8-phased-order)
9. [Effort estimate](#9-effort-estimate)
10. [Open decisions](#10-open-decisions)

---

## 1. Summary

| | Today | Target |
|---|---|---|
| Framework | CodeIgniter 3.4.5 + MX HMVC, two separate apps (`/` and `/cms/`) with their own `system/`, `MX/`, Ion Auth copies | One CodeIgniter 4 project (latest 4.x, PHP 8.2+), Composer-managed, namespaced modules |
| Auth | Ion Auth 2 (bcrypt `$2y$08$`, groups `superadmin`, `admin`, `contentmanager`) | CodeIgniter Shield 1.x, groups in `Config\AuthGroups`, route filters |
| CSS/JS | Bootstrap 3.4.1 (site: stock + 7,300-line `style.css`; CMS: custom Customizer build), jQuery 3.7.1 + Migrate | Bootstrap 5.3, jQuery kept only for Trumbowyg, DataTables, Select2 and the croppers; Migrate removed |
| Size | 2 public modules, 26 CMS modules, about 330 PHP files | Same features, same URLs |

**Total effort: about 97 person-days, about 116 with 20 % contingency.** With two developers and a part-time tester that is roughly 4 to 5 calendar months (see §9).

The order (see §8) keeps production working at every step:

1. Prepare inside CI3.
2. Move the public site to CI4 with its Bootstrap 3 markup unchanged, and cut it over while `/cms/` stays on CI3.
3. Restyle the public site to Bootstrap 5.
4. Port the whole CMS to CI4, Shield and Bootstrap 5, and test it on staging.
5. Cut the CMS over in one short window, with a rollback path.
6. Remove CI3.

---

## 2. Target architecture

### 2.1 One CI4 project, two route groups

Today the public site and the CMS are two full CodeIgniter copies. They duplicate `system/`, `MX/`, `Ion_auth`, `global_helper.php` and the constants, yet they read the same tables. The recommendation is one CI4 project:

```
zed-ci4/
  app/
    Config/            Zed.php (ex constants.php), Routes.php, Filters.php, Auth*.php (Shield)
    Controllers/BaseController.php
    Helpers/zed_helper.php        (ex global_helper.php, both apps merged)
    Libraries/UploadService.php   (all five upload paths, see §7.2)
    Models/                       one model per table, shared by site and CMS
    Validation/ZedRules.php       (ex callback_checkduplicate*)
    Views/layouts/site.php, layouts/cms.php, errors/
  modules/
    Site/Home/  {Config/Routes.php, Controllers/, Views/}
    Site/Sitemap/
    Cms/News/   {Config/Routes.php, Controllers/, Models/ (if module-specific), Views/}
    Cms/...     (26 modules)
  public/        index.php, assets/, uploads/, photogallery/, slider/   (web root)
  writable/
  vendor/        codeigniter4/framework, codeigniter4/shield, phpmailer/phpmailer
```

- `app/Config/Autoload.php` maps `'Modules' => ROOTPATH . 'modules'`. CI4 module discovery then loads each `modules/**/Config/Routes.php` automatically.
- **CMS routes** sit in a `cms` group (`$routes->group('cms', ['filter' => 'session'], …)`), so the admin URLs (`/cms/news`, `/cms/news/addedit/<id>`, `/cms/auth/login`) do not change.
- **Auto-routing stays off**, the CI4 default. Every URL is declared, which also closes the CI3 side-door URLs such as `/home/page/<slug>` and `/home/posts/<slug>`.
- **Sessions.** The public site uses sessions only for flash messages and CSRF. A single session config (`zed_session`, `SameSite=Lax`, `Secure` behind HTTPS) is acceptable for both.
- **Why not two CI4 apps?** It would keep the duplication and the two copies of the models. The single app costs nothing extra, because the CMS cut-over can still happen separately: see Phase 2 and Phase 6, where the web server sends `/cms/` to the old CI3 front controller until the CMS cutover.

### 2.2 Web root and uploads

The CMS writes files into the **public site's** `uploads/`, `photogallery/` and `slider/` (`STORAGE_FOLDER = dirname(FCPATH)`). The `media` module is the exception: it writes to `cms/uploads/`.

In CI4 these folders move under `public/`, keeping the same URLs. Their script-blocking `.htaccess` files move with them, and the nginx rules in `UPGRADE-NOTES.md` stay valid. Until the CMS cutover, the CI3 CMS must write into the same physical folders. Its `STORAGE_FOLDER` constant is the one line to repoint, or a symlink can do it.

---

## 3. Inventory and CI4 mapping

Legend for "Uploads / editor":
- **IMG**: CI3 Upload library, image types.
- **DOC**: Upload library with `ZED_DOC_TYPES`.
- **B64**: cropper data-URI via `save_base64_image()`.
- **TBW**: Trumbowyg editor, with its `tbwimage` upload endpoint when marked "+ upload".
- **MULTI**: blueimp jQuery File Upload.

### 3.1 Shared framework files (both apps)

| CI3 file (both apps unless noted) | What it does | CI4 equivalent |
|---|---|---|
| `system/` (CI 3.4.5 fork) ×2 | Framework | `vendor/codeigniter4/framework` via Composer |
| `application/third_party/MX/*` (8 files) ×2 | HMVC: module loading, routing, `Modules::run` | Removed. Native namespaces plus route discovery (`Config\Modules`) |
| `core/MY_Controller.php` ×2 | `$this->data` bag, `_render_page()` | `App\Controllers\BaseController`: `$data` property, `render(string $view)` using `view()` with a layout |
| `core/MY_Loader.php`, `core/MY_Router.php` ×2 | MX glue | Removed |
| `libraries/Ion_auth.php`, `models/Ion_auth_model.php`, `libraries/Bcrypt.php`, `config/ion_auth.php`, `language/*/ion_auth_lang.php`, `language/*/auth_lang.php`, `migrations/001_install_ion_auth.php` ×2 | Auth. Only the CMS really uses it; the public copy is dead weight | `codeigniter4/shield` (see §5). Public-site copies are deleted |
| `helpers/global_helper.php` (site) | `global_datetime()`, `get_template()`, `sendEmail()` (PHPMailer 7) | `app/Helpers/zed_helper.php`. `sendEmail()` becomes `App\Libraries\Mailer` on PHPMailer from Composer, or CI4 `Email` |
| `helpers/global_helper.php` (CMS) | `save_base64_image()`, `global_datetime()`, `get_template()` (picks the template by group), `render_bread_crumb()`, `image_upload()`, `gallery_upload()`, `save_file()`, `crypto_rand_secure()`, `getToken()` | Upload functions go to `App\Libraries\UploadService`. `render_bread_crumb()` becomes a View Cell or helper with Bootstrap 5 `breadcrumb-item`. `get_template()` is replaced by one CMS layout with a role-based sidebar. `getToken()`/`crypto_rand_secure()` become `bin2hex(random_bytes())` |
| `config/constants.php` ×2 (`TBL_*`, `URL_*`, `UPLOAD_DIR_*`, `EDITOR_PATH`, `zed_detect_root_url()`) | Table names, paths and URLs | `app/Config/Zed.php` for paths and URLs. Table names move into each Model's `$table`. `zed_detect_root_url()` is replaced by `app.baseURL` in `.env` |
| `config/config.php`, `database.php`, `autoload.php`, `routes.php` ×2 | Config | `.env` plus `Config\App`, `Database`, `Autoload`, `Routes`. CSRF moves to `Config\Security` + `Filters`, sessions to `Config\Session`, cookies to `Config\Cookie` |
| `config/routes.php` (site) | Opens a second DB connection and builds one route per page/post slug | Static routes plus a catch-all slug resolver (see §7.1) |
| `views/templates/web/{template,header,footer}.php` | Public layout | `app/Views/layouts/site.php` plus `site/header.php`, `site/footer.php` partials (`$this->extend` / `section`) |
| `views/templates/{superadmin,admin,contentmanager}/{template,top,left,right}.php` (12 files) | Three near-identical CMS layouts that differ in the sidebar menu | One `layouts/cms.php`. The sidebar is built from a menu array filtered by `auth()->user()->inGroup()` |
| `views/errors/{html,cli}/*` ×2 | Error pages | `app/Views/errors/*` (CI4 ships its own; re-skin the html ones) |
| `controllers/Welcome.php` (site, already deleted in the working tree) | Default sample | Not ported |
| `application/modules/home/views/style.css`, `responsive.css` | Stale copies inside the views folder; the template loads `assets/css/*` | Not ported (dead) |
| `phpmailer/` (vendored 7.1.1) | Mail | `composer require phpmailer/phpmailer` |
| `elearning/` (only `.htaccess`) | Empty placeholder | Copy as-is under `public/` if still needed |

### 3.2 Public site (`application/modules`)

| Module | Controller → actions | Model | Views | CI4 target |
|---|---|---|---|---|
| **home** | `Home` (330 lines): `index`, `page($slug)`, `posts($slug)`, `contactus`, `video`, `gallery`, `gallery_month`, `search`, `disciplineparameter` (AJAX POST), `zedmatrixparameter` (AJAX POST), `zedguidance` (AJAX GET), `news`, plus private `_dashboard_counts()` (file cache, remote API) and `_callAPI()` (cURL). The constructor loads menu, global config, counters, footer news and footer gallery on every request. | `Home_model` (205 lines, 30 methods; 3 raw SQL with `GROUP_CONCAT`/`FIND_IN_SET`, the rest Query Builder) | `index` (688 lines), `page` (282), `post`, `news`, `news_block`, `faq`, `incentives`, `contactus`, `gallery`, `individual-gallery`, `video`, `search`, `guidance-document`, `parameter-sector`, `zed-maturity`, `zoho_form` (960) | `Modules\Site\Home\Controllers\{Home, Pages, Gallery, Contact, Search, ZedApi}`. Data access moves to shared `App\Models\{PageModel, PostModel, MenuModel, MediaModel, GlobalConfigModel, ParameterModel, …}`. Header/footer data comes from a View Cell (`SiteChrome::render`) cached for 5 minutes, instead of being loaded in every constructor. `_dashboard_counts()` uses the `cache()` service; `_callAPI()` uses `\Config\Services::curlrequest()` with timeouts. |
| **sitemap** | `Sitemap::index` (XML of pages and posts) | none (inline Query Builder) | none | `Modules\Site\Sitemap\Controllers\Sitemap`, returning `$this->response->setXML()`. Add the `sitemap.xml` alias |

Public URLs that must keep working, all declared explicitly in CI4:
- `/` and `/<page-slug>`, `/<post-slug>`
- `/contact-us`, `/video`, `/gallery`, `/gallery/(:num)/(:segment)`, `/search`, `/news`, `/sitemap`
- `/home/disciplineparameter`, `/home/zedmatrixparameter`, `/home/zedguidance`
- `/home/page/<slug>` and `/home/posts/<slug>`: reachable today through MX auto-routing; add 301 redirects to the canonical slug

### 3.3 CMS (`cms/application/modules`)

Each module maps to `Modules\Cms\<Name>`, with `Controllers/<Name>.php`, `Views/*.php`, `Config/Routes.php` (group `cms/<name>`) and a route filter from the "Access" column. Models move to shared `App\Models\*`, where the public site needs the same table, or stay module-local.

"Rules" counts the `set_rules()` calls. "Port" and "BS5" are effort in person-days (see §9).

| Module | Controller → actions | Model → CI4 model | Views (lines) | Rules / callbacks | Uploads / editor | Access (Ion Auth groups → Shield filter) | Port | BS5 |
|---|---|---|---|---|---|---|---|---|
| auth | `Auth` (874 lines): index (user list), login, logout, change_password, forgot_password, reset_password, activate, deactivate, create_user, edit_user, create_group, edit_group, plus CSRF nonce helpers | `Ion_auth_model` → Shield `UserModel` | login, forgot_password, reset_password, change_password, index, create_user, edit_user, deactivate_user, create_group, edit_group, 3 email templates (459) | 28 | none | login is public; user and group management need `is_admin()`, which here means **superadmin** (`admin_group = 'superadmin'`) → `group:superadmin` | 3 | 2 |
| categories | index, bulk_actions, checkduplicatecategory, addedit | `Category_model` → `App\Models\CategoryModel` | index, addedit (131) | 1 / `checkduplicatecategory` | none | superadmin, admin, contentmanager | 0.75 | 0.5 |
| contactlist | index, bulk_actions | inline Query Builder → `ContactModel` | index (72) | none | none | superadmin, admin | 0.5 | 0.25 |
| dashboard | index, profile, addedit (own profile) | inline Query Builder | index, profile (100) | 4 | none | superadmin, admin, contentmanager | 0.75 | 0.5 |
| disciplines | index, bulk_actions, checkduplicatediscipline, addedit | `Discipline_model` → `DisciplineModel` | index, addedit (205) | 1 / `checkduplicatediscipline` | none | superadmin, admin | 0.75 | 0.5 |
| faq | index, bulk_actions, checkduplicatefaq, addedit, tbwimage | `Faq_model` → `FaqModel` | index, addedit (272) | 1 / `checkduplicatefaq` | TBW + upload | superadmin, admin | 1 | 0.75 |
| gallery | index, bulk_actions, addedit | `Gallery_model` → `MediaModel` (shared) | index, addedit (256) | none | IMG via `gallery_upload()` → `photogallery/` | superadmin, admin | 1 | 0.5 |
| global_config | index (SEO), gallery, home, counters, address | inline Query Builder on `zed_global_config` (label/value) → `GlobalConfigModel` | index, gallery, home, counters, address (928; 37 `$this->` calls inside views) | none | none | superadmin, admin, contentmanager | 1.5 | 1.5 |
| guidance | index, bulk_actions, addedit | `Guidance_model` → `GuidanceModel` | index, addedit (199) | 2 / `checkduplicatesector` (copy-pasted name) | none | superadmin, admin | 0.75 | 0.5 |
| homeslider | index, bulk_actions, addedit | `Slider_model` → `SliderModel` | index, addedit (353) | none | IMG via `image_upload()` → `slider/`; B64 cropper; TBW (no upload) | superadmin, admin | 1.5 | 1 |
| homevideos | index, bulk_actions, addedit | `Homevideos_model` → `HomeVideoModel` | index, addedit (260) | none | none | superadmin, admin | 0.75 | 0.5 |
| incentives | index, bulk_actions, addedit | `Incentives_model` → `IncentiveModel` | index, addedit (316) | none | IMG → `uploads/incentives/` (stores a relative path) | superadmin, admin | 1 | 0.5 |
| media | index, do_upload, addedit, bulk_actions | `Media_model` (empty file) → `MediaModel` | index, addedit (161) | none | MULTI → **`cms/uploads/`** (differs from every other module) | superadmin, admin | 1.5 | 0.5 |
| menu | index, bulk_actions, checkduplicatemenu, addedit | `Menu_model` → `MenuModel` (2 raw SQL with `FIND_IN_SET` on `menu_related_pages`) | index, addedit (310) | 1 / `checkduplicatemenu` | none (Select2 multi) | superadmin, admin | 1.25 | 0.75 |
| news | index, bulk_actions, checkduplicatepost (rule commented out), addedit (3 post kinds: article, external link, file), tbwimage, news_block | `Posts_model` → `PostModel`, `NewsBlockModel` | index, addedit (759), news_block (864 total) | 1 (disabled) | B64 cropper → `photogallery/`; DOC via `save_file()`; TBW + upload | superadmin, admin, contentmanager | 2.5 | 1.5 |
| notifications | index, bulk_actions, addedit | `Notification_model` → `NotificationModel` | index, addedit (263) | none | DOC → `uploads/` | superadmin, admin | 1 | 0.5 |
| pages | index, bulk_actions, checkduplicatepage, addedit, tbwimage, plus slug clash check against posts | `Pages_model` → `PageModel` (shared) | index, addedit (580) | 1 / `checkduplicatepage` | TBW + upload | superadmin, admin, contentmanager | 1.75 | 1.25 |
| parameter | index, bulk_actions, checkduplicateparameter, addedit | `Parameter_model` → `ParameterModel` (sector and discipline joins) | index, addedit (461) | 1 / `checkduplicateparameter` | none | superadmin, admin | 1.25 | 1 |
| programs | index, bulk_actions, addedit | `Program_model` → `ProgramModel` | index, addedit (316) | none | IMG → `uploads/programs/` | superadmin, admin | 1 | 0.5 |
| rating | index, bulk_actions, addedit | `Rating_model` → `RatingModel` | index, addedit (320) | 2 / `checkduplicatesector` | none | superadmin, admin | 0.75 | 0.75 |
| ratingimages | index, bulk_actions, addedit | `Ratingimages_model` → `RatingImageModel` | index, addedit (86) | none | IMG → `uploads/rating_images/` | superadmin, admin | 0.5 | 0.25 |
| ratingprocess | index, bulk_actions, addedit (near-copy of ratingimages) | `Ratingprocess_model` → `RatingProcessModel` | index, addedit (86) | none | IMG → `uploads/rating_images/` | superadmin, admin | 0.5 | 0.25 |
| resources | index, bulk_actions, addedit | `Resources_model` → `ResourceModel` | index, addedit (84) | none | DOC ×2 → `uploads/` | superadmin, admin | 0.75 | 0.25 |
| sector | index, bulk_actions, checkduplicatesector, addedit | `Sector_model` → `SectorModel` | index, addedit (199) | 1 / `checkduplicatesector` | none | superadmin, admin | 0.75 | 0.5 |
| stakeholders | index, bulk_actions, addedit | `Stakeholders_model` → `StakeholderModel` | index, addedit (237) | none | IMG via `gallery_upload()` → `photogallery/` | superadmin, admin | 0.75 | 0.5 |
| testimonials | index, bulk_actions, addedit | `Testimonials_model` → `TestimonialModel` | index, addedit (355) | none | IMG → `uploads/testimonials/`; TBW (no upload) | superadmin, admin | 1 | 0.75 |
| **Total (26)** | | | | | | | **28.5** | **18.25** |

Patterns shared by almost every CMS module:
- **Constructor guard.** `if (!logged_in() || !in_group($groups)) redirect('auth/login')` becomes a route filter.
- **Soft delete.** `bulk_actions` sets `status = 2` for the posted `ids[]`. This becomes `BaseCrudController::bulkDelete()` plus a model scope `->where('status !=', 2)`. CI4's `$useSoftDeletes` (which uses `deleted_at`) does **not** fit this convention, so keep the custom scope.
- **Audit columns.** `created_on`, `created_by`, `modified_on` and `modified_by` are set by hand in every `addedit`. In CI4 use `$useTimestamps` with `$createdField = 'created_on'`, `$updatedField = 'modified_on'`, and a `beforeInsert`/`beforeUpdate` callback that sets `*_by = auth()->id()`.
- **Edit IDs.** IDs are `base64_encode`d in edit URLs (`addedit/<b64>`). Keep this for one release so bookmarks work, then switch to plain `(:num)`.
- **Status checkbox.** `status = (post('x_status') == 'on') ? 1 : 0` becomes a small helper.

---

## 4. CI3 to CI4 differences that touch this code

### 4.1 Query Builder and database

| CI3 (as used here) | CI4 | Note |
|---|---|---|
| `$this->db->select(..)->where(..)->get(TBL_X)->result()` | `$this->db->table(TBL_X)->select(..)->where(..)->get()->getResult()`, or a Model: `$model->where(..)->findAll()` | The builder is per table. Calls written as separate statements (`$this->db->select(); $this->db->from(); $this->db->get();`, used in `Home_model`) must hold one `$builder` variable |
| `result()`, `result_array()`, `row()`, `row_array()` | `getResult()`, `getResultArray()`, `getRow()`, `getRowArray()` | Mechanical rename |
| `num_rows()` | `getNumRows()`, or better `countAllResults()` | `Home_model::search` |
| `affected_rows()`, `insert_id()` | `$db->affectedRows()`, `$db->insertID()` | Used in every `bulk_actions` |
| `where_in`, `order_by`, `group_by`, `or_where` | `whereIn`, `orderBy`, `groupBy`, `orWhere` | camelCase |
| `where('status!=', 2)` | `where('status !=', 2)` | Works; normalise the spacing |
| `select('…concat_ws("-",…)…')` | `select('…', false)` | CI4 tries to protect identifiers inside expressions; pass `false` wherever SQL functions appear (`Home_model::allparameter`, `disciplineresults`, `zedmaturity`, `gallery`) |
| `where(array('Year(created_on)' => $y, 'MONTHNAME(created_on)' => $m))` | `where('YEAR(created_on)', $y)`; check the generated SQL, or use a bound `query()` | Function-wrapped keys are fragile under identifier protection. **Never** pass `false` as the escape argument with user values |
| `order_by('YEAR(created_on) DESC, MONTH(created_on) DESC', '', FALSE)` | `orderBy('…', '', false)` | Same signature |
| `$this->db->query($sql, $binds)` | `$db->query($sql, $binds)` | Same. Keep the raw `GROUP_CONCAT`/`FIND_IN_SET` queries (menu, home pages, news blocks) as bound raw SQL in the model |
| `$this->db->insert(TBL, $data)` with any keys | `$model->insert($data)` **drops keys missing from `$allowedFields`, without an error** | Highest-frequency silent bug in CI4 ports. Write every column into `$allowedFields`, and add a feature test per form that checks the saved row |
| `db_debug` shows an error page | `DBDebug = true` throws `DatabaseException` | Set `DBDebug` from `CI_ENVIRONMENT` |
| `require_once BASEPATH.'database/DB'.EXT; $db =& DB();` in `routes.php` | Not possible (routes load before the DB service and must not query) | See §7.1 |
| `strictOn` | `Database::$default['strictOn'] = true` | MySQL 8 `ONLY_FULL_GROUP_BY` is already handled by the queries rewritten during the upgrade |

### 4.2 Form validation

| CI3 | CI4 | Impact here |
|---|---|---|
| `$this->form_validation->set_rules(..); run()` | `$this->validate($rules)` in the controller, or `service('validation')->setRules()->run($data)` | All 28 auth rules and about 15 module rules |
| Prep rules `trim`, `xss_clean`, `strip_tags` inside rule strings | **Not supported.** CI4 rules only validate and never change data | `contactus` uses `trim\|…`. Trim inputs explicitly, for example in a `beforeInsert` model callback |
| `callback_checkduplicate*` + `set_message()` | Custom rule class `App\Validation\ZedRules::unique_active(string $value, string $params)` registered in `Config\Validation::$ruleSets`. Params: `table,column,idField,id` | 10 callbacks are copies of one pattern: case-insensitive, ignoring `status = 2`, excluding the record's own id. Built-in `is_unique` cannot ignore soft-deleted rows, so the custom rule is needed |
| `validation_errors()`, `form_error()` | `validation_list_errors()`, `validation_show_error()` | 21 view call sites |
| `set_value('f')` after a failed POST without redirect | `set_value()` / `old()`; needs `redirect()->back()->withInput()` | Views read `$this->input->post()` directly (123 calls, see §4.3) |
| `set_error_delimiters()` | `Config\Validation::$templates` (custom list/single views) | Bootstrap 5 `invalid-feedback` markup |
| `$this->input->post('x', TRUE)` (XSS filter) | `$this->request->getPost('x')`. There is no XSS filter | Escape on output with `esc()`. Editor HTML is the exception (§7.3) |
| `$this->security->xss_clean($data)` (contact form) | None | Drop it. The contact data is already `html_escape`d in the mail body; also `esc()` it in the CMS `contactlist` view |
| Upload validation inside the Upload library | Rules `uploaded[f]`, `is_image[f]`, `mime_in[f,…]`, `ext_in[f,…]`, `max_size[f,kb]` | See §7.2 |

### 4.3 Other API changes used throughout

| CI3 | CI4 | Where |
|---|---|---|
| `redirect('x')` **exits** | `return redirect()->to('x')` **must be returned**, or the code after it runs | Every `addedit`. In `News::addedit`, the "invalid image" redirect is followed by the save. Without `return`, the CI4 port would save a bad record |
| `redirect()` inside `__construct` (auth guards in 26 controllers) | Not possible (constructors cannot return a response) | Route filters: `group:superadmin,admin` |
| `show_404()` | `throw PageNotFoundException::forPageNotFound()` | `Home::page`, `Home::posts` |
| `$this->session->set_flashdata` / `flashdata` | `session()->setFlashdata()` / `getFlashdata()` | 80 view reads |
| `$this->input->post()` **in views** | Not available (a view's `$this` is the renderer) | 123 calls in 20 views, most in `news/addedit` (12), `global_config/address` (12), `parameter/addedit` (11), `pages/addedit` (9). Replace with `old('field', $details->field ?? '')` |
| `$this->ion_auth->user()->row()` **in views** | `auth()->user()` | 36 calls (sidebar, top bar, "created by" columns) |
| `$this->uri->segment(n)` | Route placeholders as method arguments | `Home::gallery_month` |
| `$this->load->view($page)` inside a template (MX resolves the module) | `$this->include("Modules\Cms\News\Views\\{$page}")` or `section('content')` | 2 templates |
| `$this->output->set_content_type()->set_output()` | `return $this->response->setXML()` / `setJSON()` / `setBody()` | Sitemap; the AJAX endpoints that `echo json_encode()` |
| `$this->load->driver('cache', …)` | `cache()` | `_dashboard_counts()` |
| `$this->security->get_csrf_hash()` | `csrf_hash()`; token name via `csrf_token()` | Public AJAX endpoints return the new token; keep that contract for `ajax_actions.js` |
| `$this->lang->line()` | `lang('Auth.loginHeading')` | Auth views only. Shield ships its own language files; drop the 30+ Ion Auth language folders |
| `$this->upload->do_upload('f')` + `data()` | `$file = $this->request->getFile('f'); $file->isValid(); $file->move($dir, $file->getRandomName())` | See §7.2 |
| `url_title()`, `base_url()`, `current_url()`, `form_open()` | Same names in CI4 helpers | Load the `url` and `form` helpers in `BaseController::$helpers` |
| `html_escape()` | `esc()` | |

---

## 5. Ion Auth to Shield

### 5.1 What exists today
- Tables: `users` (Ion Auth columns plus `first_name`, `last_name`, `company`, `phone`), `groups`, `users_groups`, `login_attempts`.
- Identity is the email. Hashes are bcrypt `$2y$08$…`, which PHP's `password_verify()` can check, so passwords can migrate without a reset.
- Groups in the data: `admin`, `members`, `superadmin`, `contentmanager`, `manager`. Only the first, third and fourth are used in code. `admin_group = 'superadmin'`, so `is_admin()` means superadmin.
- Lockout after 3 failed attempts for 600 s, per IP. Remember-me is on.
- `use_ci_email = FALSE`, and the Auth controller does not send the code itself. **Forgot-password most likely sends no email today.** Check this on staging before promising parity.

### 5.2 Mapping

| Ion Auth | Shield |
|---|---|
| `$this->ion_auth->logged_in()` | `auth()->loggedIn()` |
| `in_group(['superadmin','admin'])` | `auth()->user()->inGroup('superadmin', 'admin')`, or the route filter `group:superadmin,admin` |
| `is_admin()` | `inGroup('superadmin')` |
| `user()->row()->id` | `auth()->id()` / `user_id()` |
| `get_users_groups()->row()` (picks the CMS template) | `auth()->user()->getGroups()`, with explicit precedence superadmin > admin > contentmanager. Today it takes the first DB row, whose order is undefined |
| `login($identity, $password, $remember)` | `auth()->remember($remember)->attempt(['email' => …, 'password' => …])` |
| `logout()` | `auth()->logout()` |
| create_user, edit_user, activate, deactivate, create_group, edit_group | Custom CMS screens on `UserModel` / `$user->addGroup()` / `$user->ban()` (or `active`). Shield has no user-admin UI. **Groups become code** in `Config\AuthGroups`, so the "create group" screen goes away; group membership stays editable |
| forgot_password / reset_password | Shield's default is a **magic-link login**; the user then sets a new password. This needs working SMTP. Alternatively build a classic reset flow (about 1 extra day) |
| `login_attempts` lockout (3 per 10 min) | Shield's `auth-rates` filter throttles by IP (default 10/min). Add a small listener on `failedLogin` if per-account lockout is still required |
| `sess_regenerate(TRUE)` after login | Shield does this itself |

### 5.3 Database migration (additive, so CI3 keeps working)

Shield's stock migration creates a `users` table, which already exists. Do **not** run it as is. Instead:

1. Copy Shield's migration into `app/Database/Migrations/…_ZedShield.php`, minus the `users` table creation.
2. In the same migration, run `ALTER TABLE users ADD` for the Shield columns: `status`, `status_message`, `last_active`, `created_at`, `updated_at`, `deleted_at`, all nullable. Ion Auth ignores extra columns. `users.active` and `users.username` already exist with compatible meaning. **Check that `username` is unique**: Shield adds a unique index, so de-duplicate first if needed.
3. Create `auth_identities`, `auth_logins`, `auth_token_logins`, `auth_remember_tokens`, `auth_groups_users` and `auth_permissions_users`.
4. Write a Spark command `php spark zed:sync-users`. It must be idempotent:
   - for each `users` row, upsert an `auth_identities` row (`type = 'email_password'`, `secret = email`, `secret2 = users.password`);
   - copy `users_groups` joined to `groups.name` into `auth_groups_users` (skip `members`/`manager`, or map them as decided);
   - set `created_at` from `FROM_UNIXTIME(created_on)`.
   Run it on staging repeatedly, and once more inside the CMS cutover window, so passwords changed in CI3 meanwhile are carried over.
5. Shield's `Passwords::verify` accepts the `$2y$08$` hashes, and on each login it rehashes them with the configured cost.
6. Because user **ids stay the same**, every `created_by` and `modified_by` value in the content tables stays valid.
7. Remember-me cookies from Ion Auth are not carried over, so everyone logs in once after cutover.
8. After the rollback window (Phase 7), drop `password`, `salt`, `activation_code`, `forgotten_password_*` and `remember_code` from `users`, and drop `groups`, `users_groups` and `login_attempts`.

Also confirm that no `admin@admin.com` default account exists (already in the upgrade checklist). The dump still contains it as user id 1, so it must not be synced to Shield unless it is renamed and given a new password.

---

## 6. Bootstrap 3 to Bootstrap 5

### 6.1 Class and markup changes found in the views
About 230 Bootstrap-3-specific hits across the views, plus the CSS:

| Bootstrap 3 | Bootstrap 5 |
|---|---|
| `col-xs-*`, `col-*-offset-*` | `col-*`, `offset-*` |
| `panel`, `panel-heading`, `panel-body` | `card`, `card-header`, `card-body` |
| `glyphicon glyphicon-*` | Font Awesome (4.x is already loaded; move to FA 6 free) or Bootstrap Icons |
| `btn-default` | `btn-secondary` / `btn-outline-secondary` |
| `pull-left`, `pull-right` | `float-start`, `float-end` |
| `hidden-xs`, `visible-*` | `d-none d-sm-block` and so on |
| `img-responsive` | `img-fluid` |
| `label label-*` | `badge text-bg-*` |
| `well` | `card` / `bg-light p-3` |
| `navbar-default`, `navbar-toggle` | `navbar navbar-expand-lg`, `navbar-toggler` |
| `input-group-addon` | `input-group-text` |
| `form-group`, `control-label`, `help-block`, `form-horizontal` | `mb-3`, `form-label`, `form-text`, grid rows |
| `data-toggle`, `data-target`, `data-dismiss` | `data-bs-toggle`, `data-bs-target`, `data-bs-dismiss` |
| jQuery plugin calls `$('#m').modal('show')`, `.collapse('in')` | `bootstrap.Modal.getOrCreateInstance(el).show()`; state class `.in` → `.show` |
| `.close` | `.btn-close` |
| Breadcrumb `<li>` from `render_bread_crumb()` | `<li class="breadcrumb-item">` |

### 6.2 Public site specifics
- The 7,300-line `assets/css/style.css` and `responsive.css` override Bootstrap 3 internals: navbar, the custom container widths re-applied in the upgrade, and the grid gutters. Expect to rewrite the header and nav, the footer and the home sections in `index.php` (688 lines). Do it component by component against screenshots of the baseline.
- Accessibility widgets:
  - `contrast-style.css` (high-contrast toggle) and `font-incr.css`/`font-dec.css` must be rebuilt against the new markup.
  - Bootstrap 5 CSS variables make the contrast theme simpler: use `data-bs-theme` or a custom `[data-contrast]` scope.
- Third-party sliders (owl.carousel, swiper, flexslider, camera, lightcase, isotope, nstSlider) do not depend on Bootstrap, so keep them. Remove duplicates while at it: three slider libraries do the same job.
- `zoho_form.php` (960 lines) is an embedded third-party form. Restyle only its wrapper.

### 6.3 CMS specifics
- The CMS uses a custom Bootstrap 3 Customizer build plus three template sets. Replace them with stock Bootstrap 5.3 and one sidebar layout. A plain Bootstrap 5 sidebar is enough for 26 list/edit screens, and AdminLTE 4 would add weight for little gain.
- Plugins to swap:
  - DataTables 1.13 Bootstrap 3 styling → DataTables 2.x with `dataTables.bootstrap5`.
  - Select2 4.0.13 → keep, with the `select2-bootstrap-5-theme`.
  - jQuery UI 1.13: only used for sortable/date widgets. Check whether native `<input type="date">` can replace it. Its `$.fn.button`/`$.fn.tooltip` names collide with Bootstrap.
- Remove dead asset folders: `datatables-1.10.12`, `jquery-ui-1.12.1.custom`, `jquery-1.11.3.min.js`, `tbw/`, and whichever of `cropit`/`cropper` is unused. Do this in Phase 7, after a grep.

---

## 7. Risky areas

### 7.1 Dynamic DB-driven routes (`application/config/routes.php`)

**Today**
- On every request, before the controller runs, `routes.php` opens a **second** database connection.
- It selects all `page_slug` and all `post_slug` values, with **no status filter**, so deleted (`status = 2`) slugs are also routed and then 404 in the controller.
- It registers one regex route per slug, then closes the connection.
- Static routes (`contact-us`, `video`, `gallery`, `search`, `news`) are added afterwards and so win over a page with the same slug.
- A post slug overwrites a page slug with the same array key, so **posts win over pages**. One live clash exists in the dump: `test`.

**Problems carried into CI4**
- CI4 routes are loaded before services and are cached by route discovery, so a DB query in `Routes.php` is not possible (and is bad practice).
- Precedence must be reproduced on purpose. The dump has duplicate slugs inside each table:
  - pages: `introduction`, `an-example`, `downloads`, `zed-model`, `test`;
  - posts: 8 slugs, including several MoU news items.
  `->row()` returns an arbitrary active match.
- The `news/checkduplicatepost` rule is commented out, so duplicate post slugs can still be created. `Pages::check_slug` and `Posts::check_slug` only check the *other* table, and only `status = 1`.
- `verify-zed-certificate` is a *page* slug that also accepts **POST** (form to the external verify API). The catch-all must accept GET and POST and stay CSRF-protected.
- `faq`, `faqs` and `benefitsincentives` are page slugs that switch to dedicated views inside `Home::page`.
- Case: CI3 matches the lower-cased key case-sensitively; the controller then compares with `LOWER()`.
- Menus store `menu_external_link` values, and editor HTML contains hard-coded links. Some may point at `/home/page/<slug>` or old hosts.

**CI4 design**
```php
// modules/Site/Home/Config/Routes.php
$routes->get('/', 'Home::index');
$routes->match(['get','post'], 'contact-us', 'Contact::index');
$routes->get('video', 'Home::video');
$routes->get('gallery', 'Gallery::index');
$routes->get('gallery/(:num)/(:segment)', 'Gallery::month/$1/$2');
$routes->match(['get','post'], 'search', 'Search::index');
$routes->get('news', 'Home::news');
$routes->get('sitemap', '\Modules\Site\Sitemap\Controllers\Sitemap::index');
$routes->post('home/disciplineparameter', 'ZedApi::disciplines');
$routes->post('home/zedmatrixparameter', 'ZedApi::maturity');
$routes->get('home/zedguidance', 'ZedApi::guidance');
$routes->get('home/(page|posts)/(:segment)', 'Slug::legacyRedirect/$2');   // 301
// last, lowest priority:
$routes->match(['get','post'], '([a-z0-9-]+)', 'Slug::resolve/$1', ['priority' => 1]);
```
- `Slug::resolve($slug)` looks up `PostModel::findActiveBySlug()` first, then `PageModel::findActiveBySlug()`, which reproduces today's "post wins". It renders the post or page view, or throws 404.
- Results are cached for 5 minutes in `cache('slug_map')`. The cache is cleared by the CMS `afterInsert`/`afterUpdate` model events.
- `['priority' => 1]` together with `Config\Routing::$prioritize = true` keeps the catch-all behind module-discovered routes, including `cms/*`.

**De-risking**
- Phase 0 makes the same change **inside CI3**: one `$route['([a-z0-9-]+)'] = 'home/resolve/$1'` placed after the static routes, plus `Home::resolve()`. This removes the DB-in-routes code while production is still on CI3, and gives the CI4 port a known-good behaviour to copy.
- Also in Phase 0, de-duplicate the active slugs and enable the duplicate-slug validation in news and pages, across both tables.
- The Phase 0 URL baseline (all slugs × status code × content hash) is replayed against CI3-with-resolver and against CI4 before each cutover.

### 7.2 File uploads

**Today there are six code paths, with inconsistent storage**

| Path | Used by | Destination | What is stored in the DB |
|---|---|---|---|
| `image_upload()` helper | homeslider | `slider/` | absolute URL `URL_FOLDER.'slider/…'` |
| `gallery_upload()` helper | gallery, stakeholders | `photogallery/` | absolute URL |
| `save_file()` helper (`ZED_DOC_TYPES`) | news (file posts) | `uploads/` | absolute URL |
| Inline Upload config | incentives, programs, testimonials, ratingimages, ratingprocess, resources, notifications | `uploads/<sub>/` or `uploads/` | **relative** path (`uploads/incentives/x.jpg`) for some, absolute for others |
| `save_base64_image()` (cropper data-URI) | news featured image, homeslider | `photogallery/` / `slider/` | absolute URL |
| blueimp MULTI `Media::do_upload` | media | **`cms/uploads/`** (`FCPATH`), unlike every other path | absolute URL under `/cms/uploads/` |
| Trumbowyg `tbwimage` | pages, news, faq | `uploads/` | absolute URL inside the editor HTML |

**Problems**
- Stored URLs carry **historical hosts**: `zedv2.umon.in` (83), `zed-admin.retedys.com`, `zed-web2.retedys.com`, `zed.org.in`, `zedv3.umon.in`, and `zed.msme.gov.in` (136). Some have a Windows backslash (`https://zed.msme.gov.in\\slider/…`, 18 rows). Any change of host or of the `public/` web root breaks images.
- On a failed upload the helpers silently fall back to `existed_file`, so the user is never told about the error.
- CI4 MIME detection uses `finfo`:
  - `docx`/`xlsx`/`pptx` are detected as `application/zip` or as the OOXML types;
  - `csv` as `text/plain`;
  - old `doc`/`xls` as `application/CDFV2`.
  The `ext_in` and `mime_in` lists must be checked against `ZED_DOC_TYPES` with real sample files.
- `cms/uploads/` (31 files) partly duplicates `uploads/` (30 files). `cms/photogallery/` and `cms/slider/` hold older files. Before any cleanup, check which of these the DB still references.
- The Media multi-upload and the cropper send several requests per form. With CSRF `regenerate = true`, the second request fails unless the token is refreshed from each response (§7.3).
- Size limits: only media sets `max_size` (30 MB). The cropper posts the image as base64 in `$_POST`, so `post_max_size` must cover about 1.4× the image size.

**CI4 design**
- One `App\Libraries\UploadService` with these methods:
  - `image(UploadedFile $f, string $bucket): string`
  - `document(UploadedFile $f, string $bucket): string`
  - `dataUri(string $uri, string $bucket): string`
- Buckets map to the existing folders under `public/`. Each method validates the extension, MIME type, size and (for images) `getimagesize`, saves under `getRandomName()`, and **returns a root-relative path** (`/uploads/incentives/ab12.jpg`).
- Views print `media_url($path)`, which accepts relative paths and, during the transition, legacy absolute ones (it rewrites known old hosts).
- Errors go back to the form with `withInput()`; nothing fails silently.

**De-risking**
- Phase 0 SQL migration normalises every stored media URL to a root-relative path, **inside editor HTML too** (`page_description`, `post_description`, `faq_answer`, `slider_description`, testimonials). It also fixes the backslashes.
- Back up first, and dry-run the migration on a copy, reporting the rows it changes.
- The CI3 views are updated in the same Phase 0 release to print relative paths. This is what lets the CI3 CMS and the CI4 site, and later the CI4 CMS, share one database during the transition.
- Fix `Media::do_upload` in Phase 0 to write to the shared `uploads/` folder.
- Keep the `uploads/`, `photogallery/` and `slider/` `.htaccess` script blocks, and the nginx "never pass to PHP" rule.
- Feature tests: one upload per module and per file type, including a renamed `.php` file and a polyglot image, both of which must be rejected.

### 7.3 Trumbowyg editor

**Today**
- Trumbowyg 2.28, with these plugins on the superadmin template: upload, table, colors, cleanpaste, pasteimage and fontsize. The admin and contentmanager templates load fewer plugins: an editor field gets a different toolbar depending on who opens it.
- It is initialised separately in five views (`faq`, `pages`, `news`, `homeslider`, `testimonials`), and three of those still have older commented-out init blocks.
- Image upload posts the field `tbwimagefield` to `{module}/tbwimage` and expects `{"success":true,"data":{"link":…}}`. Only pages, news and faq have the endpoint; homeslider and testimonials have no upload.
- These three endpoints are **excluded from CSRF** (`csrf_exclude_uris`). Because the user is logged in, another site could make an admin's browser upload images.

**Problems in the migration**
- **CSRF.** CI4 checks the `X-CSRF-TOKEN` header or the posted token. Trumbowyg's upload plugin can send extra form data (`data: [{name, value}]`) or `headers`. With `regenerate = true` the page token goes stale after each editor upload, and the article form then fails on save. Options:
  - set `Config\Security::$regenerate = false` for the CMS (recommended: same-origin admin, short sessions);
  - or have the endpoint return the new hash and update both the hidden field and the editor's header from JavaScript.
- **Raw HTML output.**
  - Editor content is stored as HTML and printed raw on the public site. That must stay raw: no `esc()`, and CI4 has no XSS clean.
  - Today the only protection is that only trusted editors can log in. Add **HTML Purifier** (`ezyang/htmlpurifier`) on save. Allow what the toolbar can produce: tables, inline colour and font-size styles, `img` with the `/uploads/` source, and links.
  - Test it on all existing content first, so pages do not lose formatting.
- **pasteimage.** This plugin inlines pasted images as base64 `data:` URIs inside the HTML, which bloats `zed_pages` and `zed_posts` rows. Either convert data URIs to files on save (in the same purifier step), or drop the plugin.
- **Bootstrap 5.** Trumbowyg does not depend on Bootstrap, but Bootstrap 5's Reboot changes `button`, `label`, `table` and box-sizing defaults inside `.trumbowyg-box`. Check the dropdowns (fontsize, colours), the table plugin and the upload modal under Bootstrap 5, and when the editor sits inside a Bootstrap 5 modal or tab.
- **jQuery.** Trumbowyg, DataTables, Select2 and the croppers need jQuery, so jQuery 3.7.1 stays in the CMS. jQuery Migrate can be removed once the console shows no warnings.

**CI4 design**
- One endpoint, `POST cms/editor/upload` (filter `group:superadmin,admin,contentmanager`), backed by `UploadService::image(…, 'uploads')`. It keeps the response shape the plugin expects (`urlPropertyName: 'data.link'`).
- One `assets/js/zed-editor.js` holds a single config (all plugins, and the CSRF header from a `<meta name="csrf">`). Views just mark `<textarea data-editor>`.
- The per-module `tbwimage` routes are removed, along with the CSRF exclusions.

### 7.4 Other risks (smaller)
- **Remote calls in the request path.**
  - `_dashboard_counts()` calls `loginzed.msme.gov.in`. It is cached for 10 minutes, or 1 minute after a failure.
  - The verify-certificate page calls `login.zed.org.in`.
  - Keep the timeouts and the cache in CI4, and mock both in tests.
- **Header data on every request.** `Home::__construct` runs the menu query (`GROUP_CONCAT` + `FIND_IN_SET`), global config, footer news and gallery. Moving this to a cached View Cell is a speed gain, but the cache must be cleared on CMS saves, or the menu edits will look delayed.
- **Edge-case CSRF.** Public AJAX (`ajax_actions.js`) reads a fresh `token` from each JSON response. Keep the same JSON key, and keep the token name `csrf_test_name` (CI4's default is the same).
- **Mail.** The contact form and Shield's magic link need SMTP from the env vars (`SMTP_*`), which must already be set in production.
- **Session cookie change.** At each cutover, users get a new session (the cookie name and format change). That is harmless for the public site; CMS editors log in again.

---

## 8. Phased order

Each phase ends deployable, with production working and a tested rollback.

### Phase 0: Preparation inside CI3 (production stays CI3)
1. **Baselines**
   - A script that requests every public URL: all active page and post slugs, the static routes, the sitemap, the AJAX endpoints and 3 gallery months. It records the status code, the final URL after redirects, the `<title>` and a hash of the main content.
   - Full-page screenshots of 15 key pages at 3 widths (Playwright).
   - A CMS smoke script: log in, then list, add, edit and bulk-delete in each module, plus one upload of each type.
   - Keep all of this as the regression suite for every later phase.
2. **Data normalisation migration** (§7.2): media URLs to root-relative, including editor HTML. Back up first.
3. **Slug resolver in CI3** (§7.1).
   - Remove the DB code from `routes.php`.
   - Add `Home::resolve`.
   - De-duplicate active slugs.
   - Enable cross-table duplicate-slug validation.
4. **Trumbowyg CSRF**: send the token from the editor and drop `csrf_exclude_uris`.
5. **Fix `Media::do_upload`** so it writes to the shared `uploads/`.
6. Inventory the `menu_external_link` values that point at internal URLs.

*Rollback:* revert the release. The data migration has a reverse script, or restore the backup.

### Phase 1: CI4 foundation (no user traffic)
- Create the CI4 project (§2.1): Composer, `.env`, `Config\Zed`, shared models for all 29 tables, `BaseController`, helpers, `UploadService`, the mailer, error views.
- Set up CI: `php -l`, PHPStan level 5, and PHPUnit feature tests (`FeatureTestTrait`) against a MySQL 8.4 test database seeded from the dump.
- Deploy to staging with a production DB snapshot.

### Phase 2: Public site on CI4, Bootstrap 3 markup kept
- Port `Home` and `Sitemap` and the slug resolver.
- Copy the views with only the mechanical CI4 changes (§4.3). The CSS stays identical, so the screenshots should diff to zero.
- **Cutover:**
  - Point the vhost web root at `zed-ci4/public/`.
  - Serve `/cms/` from the old CI3 `cms/index.php` through an Apache `Alias` or an nginx `location ^~ /cms/` block.
  - Point the CI3 CMS `STORAGE_FOLDER` at `zed-ci4/public/`.
- Replay the Phase 0 baseline in production right after the switch.
- *Rollback:* switch the vhost back (seconds). Both stacks read the same, unchanged schema.

### Phase 3: Public site Bootstrap 5 (can run in parallel with Phases 4–5)
- Restyle component by component: header and nav, footer, home sections, page and post templates, gallery, forms. Rebuild the high-contrast and font-size modes.
- Compare against the baseline screenshots. Expect deliberate differences, and get sign-off from the content owner.
- *Rollback:* redeploy the Phase 2 build.

### Phase 4: CMS foundation on CI4 (staging only)
- Shield: the additive migration and `zed:sync-users` (§5.3). Run it on **production** too, since it is additive and Ion Auth ignores it. That proves it early.
- The `cms` route group and filters, the Bootstrap 5 admin layout and sidebar, flash messages and breadcrumbs, `BaseCrudController` (list, bulk soft-delete, status toggle, audit columns), the `ZedRules::unique_active` rule, the editor endpoint and `zed-editor.js`, DataTables 2 and the Select2 Bootstrap 5 theme.
- Login, logout, profile and change-password working on staging.

### Phase 5: CMS modules, in waves (staging only; production CMS stays CI3)
Each wave ends with the Phase 0 CMS smoke script passing for that wave's modules on staging, and an editor doing user acceptance testing.

| Wave | Modules | Why this order |
|---|---|---|
| A: plain CRUD, proves the pattern | contactlist, categories, sector, disciplines, homevideos, guidance, rating | No uploads, no editor. Settles `BaseCrudController`, the validation rule and the list views |
| B: file uploads | resources, notifications, programs, incentives, ratingimages, ratingprocess, gallery, stakeholders, media | Exercises every `UploadService` path, and the MIME lists with real sample files |
| C: editor, slugs and cropper (highest risk) | faq, testimonials, homeslider, menu, parameter, pages, news, global_config | Trumbowyg + purifier, the slug-cache invalidation, the base64 cropper, `FIND_IN_SET` menus, the 37 view-side `$this->` calls in global_config |
| D: users and dashboard | dashboard (and profile), auth (user list, create, edit, activate/deactivate, group membership, password reset/magic link) | Needs the Shield decisions in §10 |

**Why the CMS is not cut over module by module.** It would need CI3 (Ion Auth session) and CI4 (Shield session) to share one login. That means reading CI3's session files from CI4 or the other way round, which is fragile and a security risk. With a handful of editors, a staged port on staging followed by one short cutover window is safer. The schema stays compatible both ways, so rollback stays possible.

### Phase 6: CMS cutover
1. Announce a 30–60 minute content freeze.
2. Back up the DB.
3. Run `zed:sync-users`.
4. Remove the `/cms/` alias so CI4's `cms` route group answers.
5. Run the CMS smoke script in production.
6. Editors log in again.
7. Keep the CI3 CMS deployable behind the alias for **2–4 weeks**.
   - *Rollback:* restore the alias.
   - Content saved by CI4 during that time stays readable by CI3, because the paths are relative and the columns unchanged.
   - Users created in CI4 would need a reverse sync; avoid creating users during the window.

### Phase 7: Cleanup (after the rollback window)
- Delete the CI3 trees: `Code/zedcms/application`, `cms/application`, both `system/` folders, MX, Ion Auth and the vendored PHPMailer.
- Drop the Ion Auth tables and columns (§5.3, step 8).
- Remove jQuery Migrate, the dead asset folders (§6.3), the `media_url()` legacy-host rewrite (once the logs show no hits), the base64 edit IDs and `cms/uploads/` (after checking that nothing references it).

---

## 9. Effort estimate

The unit is person-days for a developer who knows CI4. The figures include writing the feature tests for the code being ported, and exclude the separate QA and UAT line.

### 9.1 Per area

| Area | Port to CI4 | Bootstrap 5 | Total |
|---|---|---|---|
| **Phase 0: CI3 preparation** (baseline scripts 2, data normalisation 1.5, slug resolver 0.5, Trumbowyg CSRF + media path 1) | 5 | – | **5** |
| **Phase 1: shared CI4 foundation** (skeleton, Composer, `.env`, config 1; shared models and helpers 1.5; test harness and PHPStan 1; staging pipeline 0.5) | 4 | – | **4** |
| **Public site** | | | |
| – Site layout, `BaseController`, header/footer View Cell | 1 | | |
| – Slug resolver, routes, legacy redirects | 1 | | |
| – `Home` controller (contact + mail, AJAX, verify API, counts cache) | 2.5 | | |
| – `Home_model` → shared models | 1.5 | | |
| – 16 views, mechanical port | 1.5 | 8 | |
| – Sitemap | 0.25 | | |
| – Feature tests | 1 | | |
| *Public subtotal* | *8.75* | *8* | **16.75** |
| **CMS foundation (Phase 4)** | | | |
| – Shield migration, sync command, login/logout/profile/reset | 4 | | |
| – Admin layout and sidebar, flash, breadcrumbs, DataTables 2/Select2 | | 3 | |
| – `UploadService`, editor endpoint, `zed-editor.js`, HTML Purifier | 2 | | |
| – `BaseCrudController`, `ZedRules`, model callbacks | 2 | | |
| *CMS foundation subtotal* | *8* | *3* | **11** |
| **CMS modules (26, per §3.3)** | 28.5 | 18.25 | **46.75** |
| **QA and UAT** (regression replays, cross-browser, editor UAT per wave) | | | **8** |
| **Cutovers and infrastructure** (vhost/alias config 1, public cutover 0.5, CMS cutover 1, rollback rehearsal 0.5) | | | **3** |
| **Phase 7 cleanup** | | | **2** |
| **Total** | | | **96.5** |
| **With 20 % contingency** | | | **≈ 116** |

### 9.2 Per module (CMS, sorted by effort)

| Module | Days | Main driver |
|---|---|---|
| auth | 5 | Shield-backed user admin and reset flow |
| news | 4 | 3 post kinds, cropper, editor + upload, news blocks, slug checks |
| global_config | 3 | 5 settings screens, 928 view lines, 37 `$this->` calls in views |
| pages | 3 | Editor + upload, slug checks, 512-line form |
| homeslider | 2.5 | Image + cropper + editor |
| parameter | 2.25 | 5 level comments, sector/discipline joins, 408-line form |
| media | 2 | blueimp multi-upload, path fix, CSRF across several requests |
| menu | 2 | `FIND_IN_SET` relations, Select2 multi |
| faq, testimonials | 1.75 each | Editor (+ upload for faq) |
| gallery, incentives, notifications, programs, rating | 1.5 each | One upload path or two rules each |
| categories, dashboard, disciplines, guidance, homevideos, sector, stakeholders | 1.25 each | Plain CRUD |
| resources | 1 | Document upload |
| contactlist, ratingimages, ratingprocess | 0.75 each | Small; ratingimages and ratingprocess can share one controller |

### 9.3 Calendar

Assumes two developers and a part-time tester:

| Phase | Weeks |
|---|---|
| 0 | 1.5 |
| 1 | 1 |
| 2 | 2 |
| 3 | 2 (in parallel with 4) |
| 4 | 2.5 |
| 5 | 6–7 |
| 6 | 1 |
| 7 | 1 after the rollback window |

That is about **16–18 weeks** of elapsed time to the CMS cutover. The public site is on CI4 after about 5 weeks, and on Bootstrap 5 after about 7.

---

## 10. Open decisions

1. **Password reset.** Use Shield's magic link (no extra build, needs working SMTP) or a classic reset-by-code flow (+1 day)?
2. **Lockout.** Is Shield's IP throttling enough, or must the 3-attempts-per-account lockout be kept (+0.5 day)?
3. **Unused groups.** Drop `members` and `manager`, or map them?
4. **HTML Purifier on editor content.** Recommended; it needs a content-owner review of how existing pages render after purification.
5. **Pasted images.** Convert base64 images inside editor HTML to files, or disable the `pasteimage` plugin?
6. **Public-site redesign scope.** Like-for-like Bootstrap 5 port (estimated) or a visual refresh (not estimated)?
7. **Hosting.** Confirm Apache or nginx in production, and whether the web root can be changed to `zed-ci4/public/` (Phase 2 depends on it).
