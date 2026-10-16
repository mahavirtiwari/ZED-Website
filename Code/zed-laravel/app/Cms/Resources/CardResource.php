<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;

/** Home page cards with image, caption and button: Initiatives and Benefits/Incentives. */
abstract class CardResource extends Resource
{
    public static string $orderBy = 'sorting';

    public static string $orderDirection = 'asc';

    abstract protected static function imageDir(): string;

    public static function fields(): array
    {
        return [
            Field::text('name', 'Title')->required()->max(200),
            Field::text('caption', 'Caption')->max(200),
            Field::text('button_name', 'Button text')->max(200)->width(6),
            Field::url('link', 'Button link')->width(6)->placeholder('https://'),
            Field::image('image', 'Image')->dir(static::imageDir()),
            Field::number('sorting', 'Order')->width(4),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['image' => 'image:Image', 'name' => 'Title', 'sorting' => 'Order', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['name'];
    }
}
