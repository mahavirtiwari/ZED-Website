<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * NIC sectors (zed_sector).
 */
class Sector extends Model
{
    use ZedContent;

    protected $table = 'zed_sector';

    protected $primaryKey = 'sectorid';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['sectorid'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'code' => '',
        'parentcode' => '',
        'name' => '',
    ];

    protected $casts = [

    ];
}
