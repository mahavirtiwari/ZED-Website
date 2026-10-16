<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Options;
use App\Cms\Resource;
use App\Cms\Slugs;
use App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PageResource extends Resource
{
    public static string $model = Page::class;

    public static string $slug = 'pages';

    public static string $title = 'Pages';

    public static string $singular = 'Page';

    public static string $icon = 'bi-file-earmark-richtext';

    public static array $roles = ['superadmin', 'admin', 'contentmanager'];

    public static string $orderBy = 'created_on';

    public static function fields(): array
    {
        return [
            Field::text('page_name', 'Page name')->required()->max(200),
            Field::select('page_type', 'Page type', ['1' => 'Individual page', '2' => 'Tabbed page (with side menu)', '3' => 'Home page block'])->required()->width(6),
            Field::select('page_menu', 'Side menu', fn () => [0 => '— none —'] + Options::menus())->width(6)->help('For tabbed pages: the menu whose pages are listed at the side.'),
            Field::editor('page_description', 'Content'),
            Field::textarea('page_short_description', 'Home page short description')->showIf('page_type', ['3']),
            Field::number('page_home_order', 'Home page order')->showIf('page_type', ['3'])->width(4),
            Field::text('page_seo_title', 'SEO title')->max(200),
            Field::textarea('page_seo_keywords', 'SEO keywords')->width(6),
            Field::textarea('page_seo_description', 'SEO description')->width(6),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['page_name' => 'Page', 'page_slug' => 'URL', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['page_name', 'page_slug'];
    }

    public static function beforeSave(Model $record, Request $request): ?string
    {
        /** @var Page $record */
        if ($record->page_slug === '' || $record->isDirty('page_name')) {
            $record->page_slug = Slugs::make($record->page_name);
        }
        if ($record->page_slug === '') {
            return 'The page name must contain letters or digits.';
        }
        if (Slugs::taken($record->page_slug, page: $record)) {
            return 'The URL "'.$record->page_slug.'" is already used by another page or news item. Please change the page name.';
        }

        return null;
    }

    public static function publicUrl(Model $record): ?string
    {
        return $record->status === 1 ? url($record->page_slug) : null;
    }
}
