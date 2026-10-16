<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Initiatives cards (zed_programs).
 */
class Program extends Model
{
    use ZedContent;

    protected $table = 'zed_programs';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'name' => '',
        'caption' => '',
        'button_name' => '',
        'link' => '',
        'sorting' => 0,
    ];

    protected $casts = [
        'sorting' => 'integer',
    ];
}
