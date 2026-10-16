<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Home page videos (zed_homevideos).
 */
class HomeVideo extends Model
{
    use ZedContent;

    protected $table = 'zed_homevideos';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'video_title' => '',
        'video_link' => '',
        'video_caption' => '',
        'video_sorting' => 0,
    ];

    protected $casts = [
        'video_sorting' => 'integer',
    ];
}
