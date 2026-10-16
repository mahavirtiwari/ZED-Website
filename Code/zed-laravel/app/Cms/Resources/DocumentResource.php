<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\ResourceDocument;

class DocumentResource extends Resource
{
    public static string $model = ResourceDocument::class;

    public static string $slug = 'resources';

    public static string $title = 'Resources';

    public static string $singular = 'Resource';

    public static string $icon = 'bi-folder2-open';

    public static function fields(): array
    {
        return [
            Field::text('resources_name', 'Title')->required()->max(250),
            Field::file('resources_slug', 'File')->required()->dir('uploads')->urlStyle('root'),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['resources_name' => 'Title', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['resources_name'];
    }
}
