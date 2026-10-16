<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\Stakeholder;

class StakeholderResource extends Resource
{
    public static string $model = Stakeholder::class;

    public static string $slug = 'partners';

    public static string $title = 'Our partners';

    public static string $singular = 'Partner';

    public static string $icon = 'bi-people';

    public static function fields(): array
    {
        return [
            Field::text('stakeholder_title', 'Name')->required()->max(200),
            Field::image('stakeholder_slug', 'Logo')->required()->dir('photogallery')->urlStyle('root')->nameColumn('stakeholder_logo'),
            Field::textarea('stakeholder_caption', 'Caption'),
            Field::url('stakeholder_link', 'Website')->max(250)->placeholder('https://'),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['stakeholder_slug' => 'image:Logo', 'stakeholder_title' => 'Name', 'status' => 'badge:Status'];
    }
}
