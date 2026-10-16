<?php

namespace App\Cms;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Support\Str;

/**
 * Pages and news share one URL space (https://site/{slug}), so a slug must be
 * unique across both tables (ignoring deleted rows).
 */
class Slugs
{
    /** Same shape as the old CodeIgniter url_title(strtolower($name)). */
    public static function make(string $name): string
    {
        return Str::slug($name);
    }

    /** Fixed site URLs a page or news slug may not take. */
    public const RESERVED = ['cms', 'contact-us', 'video', 'gallery', 'search', 'news', 'sitemap', 'sitemap.xml', 'home', 'captcha', 'assets', 'uploads', 'photogallery', 'slider', 'vendor', 'js', 'css', 'up'];

    public static function taken(string $slug, ?Page $page = null, ?Post $post = null): bool
    {
        if (in_array($slug, self::RESERVED, true)) {
            return true;
        }
        $pages = Page::query()->where('page_slug', $slug);
        if ($page?->exists) {
            $pages->whereKeyNot($page->getKey());
        }
        $posts = Post::query()->where('post_slug', $slug);
        if ($post?->exists) {
            $posts->whereKeyNot($post->getKey());
        }

        return $pages->exists() || $posts->exists();
    }
}
