<?php

namespace App\Cms\Resources;

use App\Models\Program;

class ProgramResource extends CardResource
{
    public static string $model = Program::class;

    public static string $slug = 'initiatives';

    public static string $title = 'Our initiatives';

    public static string $singular = 'Initiative';

    public static string $icon = 'bi-lightning';

    protected static function imageDir(): string
    {
        return 'uploads/programs';
    }
}
