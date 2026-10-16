<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Site menus (zed_menu). menu_type: 0 single page, 1 drop-down, 2 footer, 3 external link.
 * menu_related_pages is a comma list of zed_pages ids, in display order.
 */
class Menu extends Model
{
    use ZedContent;

    protected $table = 'zed_menu';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'menu_name' => '',
        'menu_external_link' => '',
        'menu_order' => 0,
        'menu_type' => '',
    ];

    protected $casts = [
        'menu_order' => 'integer',
    ];
}
