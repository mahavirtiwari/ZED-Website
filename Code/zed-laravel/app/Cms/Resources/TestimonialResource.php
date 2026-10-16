<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\Testimonial;

class TestimonialResource extends Resource
{
    public static string $model = Testimonial::class;

    public static string $slug = 'testimonials';

    public static string $title = 'Testimonials';

    public static string $singular = 'Testimonial';

    public static string $icon = 'bi-chat-quote';

    public static string $orderBy = 'sorting';

    public static string $orderDirection = 'asc';

    public static function fields(): array
    {
        return [
            Field::text('name', 'Name')->required()->max(100)->width(6),
            Field::text('designation', 'Designation')->required()->max(100)->width(6),
            Field::text('org_name', 'Organisation')->required()->max(255)->width(6),
            Field::text('location', 'Location')->max(200)->width(6),
            Field::editor('description', 'Testimonial')->required(),
            Field::image('profile', 'Photo')->dir('uploads/testimonials'),
            Field::number('sorting', 'Order')->width(4),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['profile' => 'image:Photo', 'name' => 'Name', 'org_name' => 'Organisation', 'sorting' => 'Order', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['name', 'org_name'];
    }
}
