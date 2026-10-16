<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * Content pages (zed_pages), served at /{page_slug}.
 * page_type: 1 individual page, 2 page with side menu, 3 home page block.
 */
class Page extends Model
{
    use ZedContent;

    protected $table = 'zed_pages';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'page_name' => '',
        'page_slug' => '',
        'page_description' => '',
        'page_short_description' => '',
        'page_home_order' => '',
        'page_type' => '',
        'page_menu' => 0,
        'page_seo_title' => '',
        'page_seo_keywords' => '',
        'page_seo_description' => '',
    ];

    protected $casts = [
        'page_menu' => 'integer',
    ];
}
