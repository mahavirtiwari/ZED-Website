<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Options;
use App\Cms\Resource;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MenuResource extends Resource
{
    public static string $model = Menu::class;

    public static string $slug = 'menus';

    public static string $title = 'Menus';

    public static string $singular = 'Menu';

    public static string $icon = 'bi-list-nested';

    public static string $orderBy = 'menu_order';

    public static string $orderDirection = 'asc';

    public const TYPES = ['0' => 'Single page', '1' => 'Drop-down (several pages)', '2' => 'Footer links', '3' => 'External link'];

    public static function fields(): array
    {
        return [
            Field::text('menu_name', 'Menu name')->required()->max(200),
            Field::select('menu_type', 'Menu type', self::TYPES)->required()->default('1')->width(6),
            Field::number('menu_order', 'Order')->width(6),
            Field::multiselect('menu_related_pages', 'Pages (in display order)', fn () => Options::pages())->ordered()
                ->showIf('menu_type', ['0', '1', '2'])->help('A single-page menu uses the first page.'),
            Field::url('menu_external_link', 'External link')->required()->max(300)->showIf('menu_type', ['3'])->placeholder('https://'),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['menu_name' => 'Menu', 'menu_type' => 'Type', 'menu_order' => 'Order', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['menu_name'];
    }

    public static function beforeSave(Model $record, Request $request): ?string
    {
        $duplicate = Menu::query()->whereRaw('LOWER(menu_name) = ?', [mb_strtolower($record->menu_name)])
            ->where('menu_type', $record->menu_type)
            ->when($record->exists, fn ($q) => $q->whereKeyNot($record->getKey()))->exists();

        return $duplicate ? 'A menu with this name and type already exists.' : null;
    }
}
