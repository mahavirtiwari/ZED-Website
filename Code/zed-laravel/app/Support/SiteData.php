<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Queries behind the public site (ported from the CodeIgniter Home_model).
 * They return the same shapes as before (objects or arrays), because the
 * public templates were carried over with the same markup.
 */
class SiteData
{
    /** Top navigation: menus with their pages as comma lists (in menu order). */
    public static function menu(): array
    {
        return DB::select('SELECT A.menu_name, A.id, A.menu_type, A.menu_external_link, A.menu_order,
                GROUP_CONCAT(B.page_slug ORDER BY FIND_IN_SET(B.id, A.menu_related_pages)) AS page_slug,
                GROUP_CONCAT(B.page_name ORDER BY FIND_IN_SET(B.id, A.menu_related_pages)) AS page_name
            FROM zed_menu A LEFT JOIN zed_pages B ON FIND_IN_SET(B.id, A.menu_related_pages)
            WHERE A.status = 1 AND (A.menu_type = 3 OR (A.menu_type != 3 AND B.status = 1))
            GROUP BY A.id, A.menu_name, A.menu_type, A.menu_external_link, A.menu_order
            ORDER BY A.menu_order ASC');
    }

    /** Side menu of a tabbed page. */
    public static function pageMenus(int $menuId): array
    {
        return DB::select('SELECT A.menu_name, A.id, A.menu_type, A.menu_external_link, A.menu_order,
                GROUP_CONCAT(B.page_slug ORDER BY FIND_IN_SET(B.id, A.menu_related_pages)) AS page_slug,
                GROUP_CONCAT(B.page_name ORDER BY FIND_IN_SET(B.id, A.menu_related_pages)) AS page_name
            FROM zed_menu A LEFT JOIN zed_pages B ON FIND_IN_SET(B.id, A.menu_related_pages)
            WHERE B.page_menu = ? AND A.status = 1 AND (A.menu_type = 3 OR (A.menu_type != 3 AND B.status = 1))
            GROUP BY A.id, A.menu_name, A.menu_type, A.menu_external_link, A.menu_order
            ORDER BY A.menu_order ASC', [$menuId]);
    }

    /** label => value from zed_global_config, plus the live dashboard counts. */
    public static function globals(): array
    {
        $details = DB::table('zed_global_config')->pluck('value', 'label')->all();
        $details['counts'] = self::dashboardCounts();

        return $details;
    }

    /** Gold/silver/bronze/pledge counts from the ZED portal, cached (it is slow). */
    public static function dashboardCounts(): array
    {
        $cached = Cache::get('zed.dashboard_counts');
        if (is_array($cached)) {
            return $cached;
        }
        $counts = [];
        try {
            $response = Http::connectTimeout(3)->timeout(10)->acceptJson()->withBody('{}', 'application/json')->post(config('zed.dashboard_counts_url'));
            $counts = $response->json('Table.0') ?? [];
        } catch (\Throwable) {
            $counts = [];
        }
        // good results for 10 minutes, retry failures after 1 minute
        Cache::put('zed.dashboard_counts', $counts, $counts === [] ? 60 : 600);

        return $counts;
    }

    public static function homePages(): array
    {
        return DB::select('SELECT A.page_name, A.page_slug, A.page_short_description, A.page_home_order, MIN(B.id) AS id
            FROM zed_pages A LEFT JOIN zed_menu B ON FIND_IN_SET(A.id, B.menu_related_pages) > 0
            WHERE A.status = 1 AND page_type = 3
            GROUP BY A.id, A.page_name, A.page_slug, A.page_short_description, A.page_home_order
            ORDER BY A.page_home_order');
    }

    private static function active(string $table, ?string $order = null, string $dir = 'asc'): \Illuminate\Database\Query\Builder
    {
        $query = DB::table($table)->where('status', 1);

        return $order ? $query->orderBy($order, $dir) : $query;
    }

    public static function slider(): array
    {
        return self::active('zed_home_slider', 'slider_sorting')->get()->all();
    }

    public static function news(): array
    {
        return self::active('zed_posts', 'id', 'desc')->get()->all();
    }

    public static function homeNews(): array
    {
        return self::active('zed_posts', 'id', 'desc')->where('post_category', 1)->where('post_latest_news', 1)->limit(2)->get()->all();
    }

    public static function breakingNews(): array
    {
        return self::active('zed_posts', 'id', 'desc')->where('post_breaking_news', 1)->get()->all();
    }

    public static function footerNews(): array
    {
        return self::active('zed_posts', 'id', 'desc')->limit(3)->get()->all();
    }

    public static function footerGallery(): array
    {
        return DB::table('zed_media')->select('media_slug', 'media_name', 'media_title', 'media_caption', 'media_seo_alt_text', 'id')
            ->where('status', '!=', 2)->orderByDesc('id')->limit(9)->get()->all();
    }

    /** One entry per month with photos (latest photo of the month). */
    public static function gallery(): array
    {
        return DB::select('SELECT MAX(media_slug) AS media_slug, MAX(media_name) AS media_name, MAX(media_title) AS media_title,
                MAX(media_caption) AS media_caption, MAX(media_seo_alt_text) AS media_seo_alt_text, MAX(id) AS id,
                YEAR(created_on) AS year, MONTHNAME(created_on) AS month
            FROM zed_media WHERE status != 2
            GROUP BY YEAR(created_on), MONTH(created_on), MONTHNAME(created_on)
            ORDER BY YEAR(created_on) DESC, MONTH(created_on) DESC');
    }

    public static function galleryMonth(int $year, string $month): array
    {
        return DB::select('SELECT media_name, media_title, media_caption, media_seo_alt_text, media_slug, id,
                YEAR(created_on) AS year, MONTHNAME(created_on) AS month
            FROM zed_media WHERE status != 2 AND YEAR(created_on) = ? AND MONTHNAME(created_on) = ?
            ORDER BY id DESC', [$year, $month]);
    }

    /** @return array<int, object>|false */
    public static function search(string $term): array|false
    {
        $rows = self::active('zed_pages')->select('id', 'page_name', 'page_description', 'page_slug')
            ->where('page_name', 'like', '%'.addcslashes($term, '%_\\').'%')->get()->all();

        return $rows === [] ? false : $rows;
    }

    public static function stakeholders(): array
    {
        return DB::table('zed_stakeholders')->where('status', '!=', 2)->orderByDesc('id')->get()->all();
    }

    public static function homeVideos(): array
    {
        return self::active('zed_homevideos', 'video_sorting')->get()->all();
    }

    public static function sectors(): array
    {
        return array_map(fn ($r) => (array) $r, self::active('zed_sector', 'code')->select('sectorid', 'code', 'name')->get()->all());
    }

    public static function allParameters(): array
    {
        return DB::select('SELECT P.parameterid, CONCAT_WS("-", P.code, P.name) AS para_code_name,
                CONCAT_WS("-", D.code, D.name) AS para_discipline, CONCAT_WS("-", S.code, S.name) AS para_sector, S.code AS para_sector_id
            FROM zed_parameter P
            JOIN zed_sector S ON P.sectorid = S.sectorid
            JOIN zed_discipline D ON P.disciplineid = D.disciplineid
            WHERE P.status = 1 ORDER BY P.parameterid DESC');
    }

    public static function disciplineResults(int $sectorId): array
    {
        return array_map(fn ($r) => (array) $r, DB::select('SELECT D.code, MIN(CONCAT_WS("-", D.code, D.name)) AS para_discipline,
                MIN(CONCAT_WS("-", P.code, P.name)) AS para_code_name, MIN(P.description) AS description
            FROM zed_parameter P
            JOIN zed_sector S ON P.sectorid = S.sectorid
            JOIN zed_discipline D ON P.disciplineid = D.disciplineid
            WHERE P.status = 1 AND P.sectorid = ?
            GROUP BY D.code, P.code ORDER BY D.code ASC, P.code ASC', [$sectorId]));
    }

    public static function maturity(int $sectorId): array
    {
        return array_map(fn ($r) => (array) $r, DB::select('SELECT P.parameterid, P.code, P.name,
                CONCAT_WS("-", D.code, D.name) AS para_discipline, CONCAT_WS("-", S.code, S.name) AS para_sector,
                P.level1_comment, P.level2_comment, P.level3_comment, P.level4_comment, P.level5_comment
            FROM zed_parameter P
            JOIN zed_sector S ON P.sectorid = S.sectorid
            JOIN zed_discipline D ON P.disciplineid = D.disciplineid
            WHERE P.status = 1 AND P.sectorid = ? ORDER BY P.code ASC', [$sectorId]));
    }

    public static function guidance(int $sectorId): array
    {
        return array_map(fn ($r) => (array) $r, DB::table('zed_guidance_document')->where('sector', $sectorId)->where('status', '!=', 2)->orderBy('id')->get()->all());
    }

    public static function ratings(): array
    {
        return array_map(fn ($r) => (array) $r, self::active('zed_ratings')->get()->all());
    }

    public static function ratingImage(): ?object
    {
        return self::active('rating_images')->first();
    }

    public static function ratingProcessImage(): ?object
    {
        return self::active('zed_rating_process')->first();
    }

    public static function programs(): array
    {
        return self::active('zed_programs', 'sorting')->get()->all();
    }

    public static function incentives(): array
    {
        return self::active('zed_incentives', 'sorting')->get()->all();
    }

    public static function notifications(): array
    {
        return self::active('zed_notifications', 'sorting')->get()->all();
    }

    public static function faqs(): array
    {
        return self::active('zed_faq', 'faq_order')->get()->all();
    }

    public static function testimonials(): array
    {
        return self::active('zed_testimonials', 'sorting')->get()->all();
    }
}
