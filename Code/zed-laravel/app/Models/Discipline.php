<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * ZED disciplines; sectorids is a comma list of zed_sector ids.
 */
class Discipline extends Model
{
    use ZedContent;

    protected $table = 'zed_discipline';

    protected $primaryKey = 'disciplineid';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['disciplineid'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'code' => '',
        'name' => '',
        'sectorids' => '',
    ];

    protected $casts = [

    ];
}
