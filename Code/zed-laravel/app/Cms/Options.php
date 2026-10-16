<?php

namespace App\Cms;

use App\Models\Discipline;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Sector;

/** Option lists shared by several CMS forms. */
class Options
{
    /** sectorid => "code-name" */
    public static function sectors(): array
    {
        return Sector::query()->orderBy('code')->get()->mapWithKeys(fn ($s) => [$s->sectorid => $s->code.'-'.$s->name])->all();
    }

    /** "code-name" => "code-name" (zed_ratings stores the text) */
    public static function sectorNames(): array
    {
        $names = array_values(self::sectors());

        return array_combine($names, $names);
    }

    public static function disciplines(): array
    {
        return Discipline::query()->orderBy('code')->get()->mapWithKeys(fn ($d) => [$d->disciplineid => $d->code.'-'.$d->name])->all();
    }

    public static function pages(): array
    {
        return Page::query()->orderBy('page_name')->pluck('page_name', 'id')->all();
    }

    public static function menus(): array
    {
        return Menu::query()->active()->orderBy('menu_order')->pluck('menu_name', 'id')->all();
    }

    public const STATES = ['AP' => 'Andhra Pradesh', 'AR' => 'Arunachal Pradesh', 'AS' => 'Assam', 'BR' => 'Bihar', 'CT' => 'Chhattisgarh', 'GA' => 'Goa', 'GJ' => 'Gujarat', 'HR' => 'Haryana', 'HP' => 'Himachal Pradesh', 'JK' => 'Jammu and Kashmir', 'JH' => 'Jharkhand', 'KA' => 'Karnataka', 'KL' => 'Kerala', 'LA' => 'Ladakh', 'MP' => 'Madhya Pradesh', 'MH' => 'Maharashtra', 'MN' => 'Manipur', 'ML' => 'Meghalaya', 'MZ' => 'Mizoram', 'NL' => 'Nagaland', 'OR' => 'Odisha', 'PB' => 'Punjab', 'RJ' => 'Rajasthan', 'SK' => 'Sikkim', 'TN' => 'Tamil Nadu', 'TG' => 'Telangana', 'TR' => 'Tripura', 'UT' => 'Uttarakhand', 'UP' => 'Uttar Pradesh', 'WB' => 'West Bengal', 'AN' => 'Andaman and Nicobar Islands', 'CH' => 'Chandigarh', 'DN' => 'Dadra and Nagar Haveli and Daman and Diu', 'DD' => 'Daman and Diu', 'DL' => 'Delhi', 'LD' => 'Lakshadweep', 'PY' => 'Puducherry'];
}
