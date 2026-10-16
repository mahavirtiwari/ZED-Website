<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\RatingProcess;

class RatingProcessResource extends Resource
{
    public static string $model = RatingProcess::class;

    public static string $slug = 'process-image';

    public static string $title = 'ZED process image';

    public static string $singular = 'Process image';

    public static string $icon = 'bi-diagram-2';

    public static function fields(): array
    {
        return [
            Field::text('name', 'Title')->required()->max(255),
            Field::image('image_path', 'Image')->required()->dir('uploads/rating_images'),
            Field::toggle('status', 'Active')->help('The home page shows the first active image.'),
        ];
    }

    public static function columns(): array
    {
        return ['image_path' => 'image:Image', 'name' => 'Title', 'status' => 'badge:Status'];
    }
}
