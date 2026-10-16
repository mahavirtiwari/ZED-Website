<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\HomeSlider;

class SliderResource extends Resource
{
    public static string $model = HomeSlider::class;

    public static string $slug = 'sliders';

    public static string $title = 'Home sliders';

    public static string $singular = 'Slider';

    public static string $icon = 'bi-images';

    public static string $orderBy = 'slider_sorting';

    public static string $orderDirection = 'asc';

    public static function fields(): array
    {
        return [
            Field::image('slider_slug', 'Image')->required()->dir('slider')->urlStyle('root')->nameColumn('slider_name')->help('Wide image, about 1920 × 700 pixels.'),
            Field::editor('slider_description', 'Caption'),
            Field::number('slider_sorting', 'Order')->width(4),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['slider_slug' => 'image:Image', 'slider_name' => 'File', 'slider_sorting' => 'Order', 'status' => 'badge:Status'];
    }
}
