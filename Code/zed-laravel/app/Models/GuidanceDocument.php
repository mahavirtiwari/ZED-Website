<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Sector-wise ZED guidance documents (zed_guidance_document).
 */
class GuidanceDocument extends Model
{
    use ZedContent;

    protected $table = 'zed_guidance_document';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'modified_at';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'd_name' => '',
        'sector' => '',
    ];

    protected $casts = [

    ];
}
