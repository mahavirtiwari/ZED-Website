<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * ZED maturity parameters (zed_parameter); sectorid is a comma list.
 */
class Parameter extends Model
{
    use ZedContent;

    protected $table = 'zed_parameter';

    protected $primaryKey = 'parameterid';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['parameterid'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'code' => '',
        'name' => '',
        'description' => '',
        'sectorid' => '',
        'disciplineid' => 0,
        'ro' => '',
        'gw' => '',
        'level1_comment' => '',
        'level2_comment' => '',
        'level3_comment' => '',
        'level4_comment' => '',
        'level5_comment' => '',
    ];

    protected $casts = [
        'disciplineid' => 'integer',
    ];
}
