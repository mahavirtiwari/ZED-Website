<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\Category;

class CategoryResource extends Resource
{
    public static string $model = Category::class;

    public static string $slug = 'categories';

    public static string $title = 'News categories';

    public static string $singular = 'Category';

    public static string $icon = 'bi-tags';

    public static array $roles = ['superadmin', 'admin', 'contentmanager'];

    public static function fields(): array
    {
        return [
            Field::text('category_name', 'Category name')->required()->max(150),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['category_name' => 'Category', 'status' => 'badge:Status'];
    }
}
