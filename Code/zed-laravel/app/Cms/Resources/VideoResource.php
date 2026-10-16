<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\HomeVideo;

class VideoResource extends Resource
{
    public static string $model = HomeVideo::class;

    public static string $slug = 'videos';

    public static string $title = 'Home videos';

    public static string $singular = 'Video';

    public static string $icon = 'bi-camera-video';

    public static string $orderBy = 'video_sorting';

    public static string $orderDirection = 'asc';

    public static function fields(): array
    {
        return [
            Field::text('video_title', 'Title')->required()->max(250),
            Field::url('video_link', 'YouTube link')->required()->placeholder('https://www.youtube.com/watch?v=...'),
            Field::text('video_caption', 'Caption')->max(250),
            Field::textarea('video_description', 'Description'),
            Field::number('video_sorting', 'Order')->width(4),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['video_title' => 'Title', 'video_sorting' => 'Order', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['video_title'];
    }
}
