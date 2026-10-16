<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Photo gallery images (zed_media).
 */
class Media extends Model
{
    use ZedContent;

    protected $table = 'zed_media';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'media_name' => '',
        'media_slug' => '',
        'media_type' => '',
        'media_title' => '',
        'media_caption' => '',
        'media_seo_alt_text' => '',
    ];

    protected $casts = [

    ];
}
