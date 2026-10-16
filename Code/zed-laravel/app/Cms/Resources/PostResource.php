<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Cms\Slugs;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PostResource extends Resource
{
    public static string $model = Post::class;

    public static string $slug = 'news';

    public static string $title = 'News';

    public static string $singular = 'News item';

    public static string $icon = 'bi-newspaper';

    public static array $roles = ['superadmin', 'admin', 'contentmanager'];

    public static function fields(): array
    {
        return [
            Field::select('post_category', 'Type', ['1' => 'Article (page on this site)', '2' => 'External link', '3' => 'File (PDF, document)'])->required()->default('1'),
            Field::text('post_name', 'Title')->required()->max(200),
            Field::editor('post_description', 'Content')->showIf('post_category', ['1']),
            Field::image('featured_slug', 'Featured image')->dir('photogallery')->urlStyle('root')->nameColumn('featured_name')->showIf('post_category', ['1']),
            Field::url('post_external_link', 'Link')->required()->max(300)->showIf('post_category', ['2'])->placeholder('https://'),
            Field::file('post_image', 'File')->dir('uploads')->urlStyle('root')->showIf('post_category', ['3']),
            Field::date('post_display_date', 'Display date')->width(4),
            Field::toggle('post_breaking_news', 'Important announcement (scrolling bar)')->default(0)->width(4),
            Field::toggle('post_latest_news', 'Latest news (home page)')->default(0)->width(4),
            Field::text('post_seo_title', 'SEO title')->max(200)->showIf('post_category', ['1']),
            Field::textarea('post_seo_keywords', 'SEO keywords')->width(6)->showIf('post_category', ['1']),
            Field::textarea('post_seo_description', 'SEO description')->width(6)->showIf('post_category', ['1']),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['post_name' => 'Title', 'post_slug' => 'URL', 'post_display_date' => 'Date', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['post_name', 'post_slug'];
    }

    public static function beforeSave(Model $record, Request $request): ?string
    {
        /** @var Post $record */
        if ((int) $record->post_category === 3 && ! $record->post_image) {
            return 'Please choose the file to link.';
        }
        if ($record->post_slug === '' || $record->isDirty('post_name')) {
            $record->post_slug = Slugs::make($record->post_name);
        }
        if ($record->post_slug === '') {
            return 'The title must contain letters or digits.';
        }
        if (Slugs::taken($record->post_slug, post: $record)) {
            return 'The URL "'.$record->post_slug.'" is already used by another page or news item. Please change the title.';
        }

        return null;
    }

    public static function publicUrl(Model $record): ?string
    {
        return $record->status === 1 && (int) $record->post_category === 1 ? url($record->post_slug) : null;
    }
}
