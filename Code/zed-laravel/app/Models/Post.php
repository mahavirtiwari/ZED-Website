<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * News (zed_posts), served at /{post_slug}.
 * post_category: 1 article, 2 external link, 3 file.
 */
class Post extends Model
{
    use ZedContent;

    protected $table = 'zed_posts';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'post_name' => '',
        'post_slug' => '',
        'featured_name' => '',
        'featured_slug' => '',
        'post_description' => '',
        'post_image' => '',
        'post_category' => 0,
        'post_external_link' => '',
        'post_breaking_news' => 0,
        'post_seo_title' => '',
        'post_seo_keywords' => '',
        'post_seo_description' => '',
    ];

    protected $casts = [
        'post_category' => 'integer',
        'post_breaking_news' => 'integer',
        'post_latest_news' => 'integer',
    ];
}
