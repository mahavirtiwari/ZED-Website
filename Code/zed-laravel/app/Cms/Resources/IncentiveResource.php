<?php

namespace App\Cms\Resources;

use App\Models\Incentive;

class IncentiveResource extends CardResource
{
    public static string $model = Incentive::class;

    public static string $slug = 'incentives';

    public static string $title = 'Benefits / incentives';

    public static string $singular = 'Benefit';

    public static string $icon = 'bi-gift';

    protected static function imageDir(): string
    {
        return 'uploads/incentives';
    }
}
