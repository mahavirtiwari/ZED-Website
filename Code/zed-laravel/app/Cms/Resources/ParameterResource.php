<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Options;
use App\Cms\Resource;
use App\Models\Parameter;

class ParameterResource extends Resource
{
    public static string $model = Parameter::class;

    public static string $slug = 'parameters';

    public static string $title = 'Parameters';

    public static string $singular = 'Parameter';

    public static string $icon = 'bi-sliders';

    public static int $perPage = 50;

    public static function fields(): array
    {
        $fields = [
            Field::text('code', 'Code')->required()->max(200)->width(4),
            Field::text('name', 'Parameter name')->required()->max(250)->width(8),
            Field::textarea('description', 'Description'),
            Field::multiselect('sectorid', 'Sectors', fn () => Options::sectors())->required(),
            Field::select('disciplineid', 'Discipline', fn () => Options::disciplines())->required()->width(6),
            Field::text('ro', 'RO')->max(100)->width(3),
            Field::text('gw', 'GW')->max(100)->width(3),
        ];
        for ($level = 1; $level <= 5; $level++) {
            $fields[] = Field::textarea('level'.$level.'_comment', 'Level '.$level)->width(6);
        }
        $fields[] = Field::toggle('status', 'Active');

        return $fields;
    }

    public static function columns(): array
    {
        return ['code' => 'Code', 'name' => 'Parameter', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['code', 'name'];
    }
}
