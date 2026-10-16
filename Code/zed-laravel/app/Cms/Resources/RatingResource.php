<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Options;
use App\Cms\Resource;
use App\Models\Rating;

class RatingResource extends Resource
{
    public static string $model = Rating::class;

    public static string $slug = 'certified-msmes';

    public static string $title = 'ZED certified MSMEs';

    public static string $singular = 'Certified MSME';

    public static string $icon = 'bi-award';

    public static int $perPage = 50;

    public static function fields(): array
    {
        return [
            Field::text('ep_name', 'Enterprise name')->required()->max(250),
            Field::url('ep_url', 'Website')->placeholder('https://'),
            Field::select('sector', 'Sector', fn () => Options::sectorNames())->required(),
            Field::select('state', 'State', array_combine(array_values(Options::STATES), array_values(Options::STATES)))->required()->width(4),
            Field::select('size', 'Size', ['micro' => 'Micro', 'small' => 'Small', 'medium' => 'Medium'])->required()->width(4),
            Field::select('rating', 'Rating', ['Bronze' => 'Bronze', 'Silver' => 'Silver', 'Gold' => 'Gold'])->required()->width(4),
            Field::textarea('unit_address', 'Unit address')->required(),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['ep_name' => 'Enterprise', 'state' => 'State', 'rating' => 'Rating', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['ep_name', 'state', 'sector'];
    }
}
