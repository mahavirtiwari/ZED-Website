<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\RatingImage;

class RatingImageResource extends Resource
{
    public static string $model = RatingImage::class;

    public static string $slug = 'rating-image';

    public static string $title = 'ZED rating image';

    public static string $singular = 'Rating image';

    public static string $icon = 'bi-bar-chart-steps';

    public static function fields(): array
    {
        return [
            Field::text('name', 'Title')->required()->max(100),
            Field::image('image_path', 'Image')->required()->dir('uploads'),
            Field::toggle('status', 'Active')->help('The home page shows the first active image.'),
        ];
    }

    public static function columns(): array
    {
        return ['image_path' => 'image:Image', 'name' => 'Title', 'status' => 'badge:Status'];
    }
}
