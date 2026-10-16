<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Options;
use App\Cms\Resource;
use App\Models\GuidanceDocument;

class GuidanceResource extends Resource
{
    public static string $model = GuidanceDocument::class;

    public static string $slug = 'guidance';

    public static string $title = 'Guidance documents';

    public static string $singular = 'Guidance document';

    public static string $icon = 'bi-journal-text';

    public static function fields(): array
    {
        return [
            Field::text('d_name', 'Document name')->required()->max(250),
            Field::url('d_url', 'Document link')->required()->placeholder('https://'),
            Field::select('sector', 'Sector', fn () => Options::sectors())->required(),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['d_name' => 'Document', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['d_name'];
    }
}
