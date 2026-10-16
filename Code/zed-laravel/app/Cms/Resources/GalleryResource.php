<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class GalleryResource extends Resource
{
    public static string $model = Media::class;

    public static string $slug = 'gallery';

    public static string $title = 'Photo gallery';

    public static string $singular = 'Photo';

    public static string $icon = 'bi-image';

    public static bool $multiUpload = true;

    public static function fields(): array
    {
        return [
            Field::image('media_slug', 'Photo')->required()->dir('photogallery')->urlStyle('root')->nameColumn('media_name')->maxKb(10240),
            Field::text('media_title', 'Title')->max(200),
            Field::textarea('media_caption', 'Caption'),
            Field::text('media_seo_alt_text', 'Alternative text (for screen readers)')->max(200),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['media_slug' => 'image:Photo', 'media_title' => 'Title', 'created_on' => 'Added', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['media_title', 'media_caption'];
    }

    public static function beforeSave(Model $record, Request $request): ?string
    {
        $record->media_type = 'image';

        return null;
    }
}
