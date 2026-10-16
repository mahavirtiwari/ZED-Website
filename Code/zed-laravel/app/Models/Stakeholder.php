<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Partners / stakeholders logos (zed_stakeholders).
 */
class Stakeholder extends Model
{
    use ZedContent;

    protected $table = 'zed_stakeholders';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'stakeholder_title' => '',
        'stakeholder_logo' => '',
        'stakeholder_slug' => '',
        'stakeholder_caption' => '',
        'stakeholder_link' => '',
    ];

    protected $casts = [

    ];
}
