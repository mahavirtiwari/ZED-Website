<?php

namespace App\Cms\Resources;

use App\Cms\Field;
use App\Cms\Resource;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FaqResource extends Resource
{
    public static string $model = Faq::class;

    public static string $slug = 'faq';

    public static string $title = 'FAQs';

    public static string $singular = 'FAQ';

    public static string $icon = 'bi-question-circle';

    public static string $orderBy = 'faq_order';

    public static string $orderDirection = 'asc';

    public static function fields(): array
    {
        return [
            Field::text('faq_question', 'Question')->required()->max(300),
            Field::editor('faq_answer', 'Answer')->required(),
            Field::number('faq_order', 'Order')->width(4),
            Field::toggle('status', 'Active'),
        ];
    }

    public static function columns(): array
    {
        return ['faq_question' => 'Question', 'faq_order' => 'Order', 'status' => 'badge:Status'];
    }

    public static function searchable(): array
    {
        return ['faq_question'];
    }

    public static function beforeSave(Model $record, Request $request): ?string
    {
        $duplicate = Faq::query()->whereRaw('LOWER(faq_question) = ?', [mb_strtolower($record->faq_question)])
            ->when($record->exists, fn ($q) => $q->whereKeyNot($record->getKey()))->exists();

        return $duplicate ? 'This question already exists.' : null;
    }
}
