<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\Sector;

class SectorResource extends Resource
{
    public static string $model = Sector::class;

    public static string $slug = 'sectors';

    public static string $title = 'Sectors';

    public static string $singular = 'Sector';

    public static string $icon = 'bi-diagram-3';

    public static string $orderBy = 'code';

    public static string $orderDirection = 'asc';

    public static function fields(): array
    {
        return [
            Field::text('code', 'NIC code')->required()->max(150)->width(4),
            Field::text('name', 'Sector name')->required()->max(250)->width(8),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['code' => 'Code', 'name' => 'Sector', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['code', 'name'];
    }
}
