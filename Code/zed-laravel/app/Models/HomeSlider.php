<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Home page slider images (zed_home_slider).
 */
class HomeSlider extends Model
{
    use ZedContent;

    protected $table = 'zed_home_slider';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'slider_description' => '',
        'slider_sorting' => 0,
    ];

    protected $casts = [
        'slider_sorting' => 'integer',
    ];
}
