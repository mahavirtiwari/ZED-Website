<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The three news blocks on the home page (zed_news_block). news_block_id matches
 * zed_posts.post_category; news_block_status is 'active' or 'inactive'.
 */
class NewsBlock extends Model
{
    protected $table = 'zed_news_block';

    protected $primaryKey = 'news_block_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['news_block_heading', 'news_block_status'];
}
