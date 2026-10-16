<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class NotificationResource extends Resource
{
    public static string $model = Notification::class;

    public static string $slug = 'notifications';

    public static string $title = 'Notifications';

    public static string $singular = 'Notification';

    public static string $icon = 'bi-megaphone';

    public static string $orderBy = 'sorting';

    public static string $orderDirection = 'asc';

    public static function fields(): array
    {
        return [
            Field::text('name', 'Title')->required()->max(200),
            Field::select('type', 'Opens', ['file' => 'An uploaded file', 'link' => 'A link'])->required()->default('file')->width(6),
            Field::file('file_path', 'File')->dir('uploads')->showIf('type', ['file']),
            Field::url('link', 'Link')->required()->showIf('type', ['link'])->placeholder('https://'),
            Field::number('sorting', 'Order')->width(4),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['name' => 'Title', 'type' => 'Opens', 'sorting' => 'Order', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['name'];
    }

    public static function beforeSave(Model $record, Request $request): ?string
    {
        return $record->type === 'file' && ! $record->file_path ? 'Please choose the file.' : null;
    }
}
