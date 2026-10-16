<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Downloadable resources (zed_resources).
 */
class ResourceDocument extends Model
{
    use ZedContent;

    protected $table = 'zed_resources';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'resources_name' => '',
        'resources_slug' => '',
    ];

    protected $casts = [

    ];
}
