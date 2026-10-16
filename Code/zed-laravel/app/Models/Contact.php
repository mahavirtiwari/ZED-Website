<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Contact Us submissions from the website form (zed_contact).
 */
class Contact extends Model
{
    use ZedContent;

    protected $table = 'zed_contact';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = null;

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'contact_name' => '',
        'contact_email' => '',
        'contact_phone' => '',
        'enterprise_name' => '',
        'query_category' => '',
        'contact_query' => '',
    ];

    protected $casts = [

    ];

    public function usesAuditColumns(): bool
    {
        return false;
    }
}
