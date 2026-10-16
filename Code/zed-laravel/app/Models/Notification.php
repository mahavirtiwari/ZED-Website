<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Notifications / circulars (zed_notifications).
 */
class Notification extends Model
{
    use ZedContent;

    protected $table = 'zed_notifications';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'name' => '',
        'file_path' => '',
        'sorting' => 0,
    ];

    protected $casts = [
        'sorting' => 'integer',
    ];
}
