<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Testimonials (zed_testimonials).
 */
class Testimonial extends Model
{
    use ZedContent;

    protected $table = 'zed_testimonials';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'name' => '',
        'designation' => '',
        'org_name' => '',
        'description' => '',
    ];

    protected $casts = [
        'sorting' => 'integer',
    ];
}
