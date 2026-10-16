<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * ZED certified MSMEs list (zed_ratings).
 */
class Rating extends Model
{
    use ZedContent;

    protected $table = 'zed_ratings';

    protected $primaryKey = 'rating_id';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'modified_at';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['rating_id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'ep_name' => '',
        'sector' => '',
        'state' => '',
        'size' => '',
        'rating' => '',
        'unit_address' => '',
    ];

    protected $casts = [

    ];
}
