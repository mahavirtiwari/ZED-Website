<?php

namespace App\Cms;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * A CMS module: one table edited through the generic list / add / edit / delete
 * screens (App\Http\Controllers\Cms\ResourceController). Subclasses declare the
 * model, the form fields and the list columns; config/cms.php lists them.
 */
abstract class Resource
{
    /** @var class-string<Model> */
    public static string $model;

    /** URL segment: /cms/{slug} */
    public static string $slug;

    /** Plural title, e.g. "Pages" */
    public static string $title;

    /** Singular, e.g. "Page" */
    public static string $singular;

    /** Bootstrap icon name (bi-*) */
    public static string $icon = 'bi-file-earmark-text';

    /** Ion Auth groups that may use this module */
    public static array $roles = ['superadmin', 'admin'];

    public static string $orderBy = '';

    public static string $orderDirection = 'desc';

    public static int $perPage = 25;

    public static bool $canCreate = true;

    public static bool $canDelete = true;

    /** Show the "upload several images" form on the list (gallery). */
    public static bool $multiUpload = false;

    /** @return array<int, Field> */
    abstract public static function fields(): array;

    /**
     * List columns: attribute => label. A value starting with "badge:" shows a
     * status badge, "image:" a thumbnail.
     * @return array<string, string>
     */
    abstract public static function columns(): array;

    /** @return array<int, string> attributes the list search looks in */
    public static function searchable(): array
    {
        return [];
    }

    public static function query(): Builder
    {
        $model = new static::$model;
        $order = static::$orderBy !== '' ? static::$orderBy : $model->getKeyName();

        return static::$model::query()->orderBy($order, static::$orderDirection);
    }

    public static function allows(?User $user): bool
    {
        return $user !== null && $user->inGroup(static::$roles);
    }

    /**
     * Last check before saving; return an error message to stop the save.
     * Also the place to set derived columns (slugs, types).
     */
    public static function beforeSave(Model $record, Request $request): ?string
    {
        return null;
    }

    /** Text shown for a record in messages. */
    public static function recordTitle(Model $record): string
    {
        foreach (array_keys(static::columns()) as $column) {
            $value = $record->getAttribute($column);
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return static::$singular.' #'.$record->getKey();
    }

    /** Public URL of a record, for the "view on site" link (null: none). */
    public static function publicUrl(Model $record): ?string
    {
        return null;
    }
}
