<?php

namespace App\Models;

use App\Models\Concerns\ZedContent;
use Illuminate\Database\Eloquent\Model;

/**
 * FAQs (zed_faq).
 */
class Faq extends Model
{
    use ZedContent;

    protected $table = 'zed_faq';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'modified_on';

    // input is whitelisted by the CMS resource definitions
    protected $guarded = ['id'];

    /** NOT NULL columns without a database default */
    protected $attributes = [
        'status' => 1,
        'faq_question' => '',
        'faq_answer' => '',
        'faq_order' => 0,
    ];

    protected $casts = [
        'faq_order' => 'integer',
    ];
}
