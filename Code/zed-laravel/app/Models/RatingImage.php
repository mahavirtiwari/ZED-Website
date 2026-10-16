<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * ZED rating image (rating_images).
 */
class RatingImage extends Model
{
    use ZedContent;

    protected $table = 'rating_images';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'name' => '',
        'image_path' => '',
    ];

    protected $casts = [

    ];
}
