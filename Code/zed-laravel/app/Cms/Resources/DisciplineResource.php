<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Options;
use App\Cms\Resource;
use App\Models\Discipline;

class DisciplineResource extends Resource
{
    public static string $model = Discipline::class;

    public static string $slug = 'disciplines';

    public static string $title = 'Disciplines';

    public static string $singular = 'Discipline';

    public static string $icon = 'bi-collection';

    public static string $orderBy = 'code';

    public static string $orderDirection = 'asc';

    public static function fields(): array
    {
        return [
            Field::text('code', 'Code')->required()->max(200)->width(4),
            Field::text('name', 'Discipline name')->required()->max(250)->width(8),
            Field::multiselect('sectorids', 'Sectors', fn () => Options::sectors()),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['code' => 'Code', 'name' => 'Discipline', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['code', 'name'];
    }
}
