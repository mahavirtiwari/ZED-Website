<?php

use App\Cms\Resources;

/*
 * CMS modules, in sidebar order. Each is a list/add/edit/delete module built
 * from its App\Cms\Resources class. Dashboard, Contacts, Settings, Security,
 * News blocks and Users have their own controllers (routes/cms.php).
 */
return [
    'resources' => [
        Resources\PostResource::class,
        Resources\PageResource::class,
        Resources\MenuResource::class,
        Resources\FaqResource::class,
        Resources\SliderResource::class,
        Resources\VideoResource::class,
        Resources\ProgramResource::class,
        Resources\IncentiveResource::class,
        Resources\NotificationResource::class,
        Resources\GalleryResource::class,
        Resources\StakeholderResource::class,
        Resources\TestimonialResource::class,
        Resources\DocumentResource::class,
        Resources\RatingResource::class,
        Resources\GuidanceResource::class,
        Resources\RatingProcessResource::class,
        Resources\RatingImageResource::class,
        Resources\ParameterResource::class,
        Resources\DisciplineResource::class,
        Resources\SectorResource::class,
        Resources\CategoryResource::class,
    ],

    // Settings screens: label => [title, roles, fields (name => [label, type])]
    'settings' => [
        'about' => ['About us', ['superadmin', 'admin', 'contentmanager'], [
            'footer_heading' => ['About us heading', 'text'],
            'about_short_description' => ['About us short description', 'textarea'],
            'footer_text' => ['About us footer text', 'textarea'],
        ]],
        'home-seo' => ['Home page SEO', ['superadmin', 'admin', 'contentmanager'], [
            'home_seo_title' => ['SEO title', 'text'],
            'home_seo_keywords' => ['SEO keywords', 'textarea'],
            'home_seo_description' => ['SEO description', 'textarea'],
        ]],
        'gallery-seo' => ['Gallery SEO', ['superadmin', 'admin', 'contentmanager'], [
            'gallery_seo_title' => ['SEO title', 'text'],
            'gallery_seo_keywords' => ['SEO keywords', 'textarea'],
            'gallery_seo_description' => ['SEO description', 'textarea'],
        ]],
        'counters' => ['Counters', ['superadmin', 'admin', 'contentmanager'], [
            'masmes_participated_in_awareness_programmes' => ['MSMEs participated in awareness programmes', 'number'],
            'msme_registered' => ['MSMEs registered', 'number'],
            'pledge' => ['Pledge', 'number'],
            'bronze_certified' => ['Bronze certified', 'number'],
            'silver_certified' => ['Silver certified', 'number'],
            'gold_certified' => ['Gold certified', 'number'],
            'dashboard_status' => ['Counters source', 'select:1=Live ZED dashboard,0=Numbers above'],
        ]],
        'address' => ['Contact details', ['superadmin', 'admin', 'contentmanager'], [
            'address' => ['Address', 'textarea'],
            'label_name_1' => ['Contact 1: name', 'text'],
            'zed_phone_1' => ['Contact 1: phone 1', 'text'],
            'zed_phone_2' => ['Contact 1: phone 2', 'text'],
            'zed_email_id' => ['Contact 1: e-mail', 'text'],
            'label_name_2' => ['Contact 2: name', 'text'],
            'tp_phone_1' => ['Contact 2: phone 1', 'text'],
            'tp_phone_2' => ['Contact 2: phone 2', 'text'],
            'tp_phone_3' => ['Contact 2: phone 3', 'text'],
            'tp_email_id' => ['Contact 2: e-mail', 'text'],
            'button_name' => ['Button text', 'text'],
            'button_url' => ['Button link', 'text'],
        ]],
        'security' => ['Security', ['superadmin', 'admin'], [
            'captcha_cms_login' => ['Captcha on CMS login and forgot password', 'select:1=Enabled,0=Disabled'],
            'contact_form_type' => ['Contact Us form', 'select:zoho=Zoho Desk form (creates a Zoho Desk ticket),site=Website form (saved in CMS Contacts and e-mailed)'],
            'captcha_contact_us' => ['Captcha on Contact Us (website form)', 'select:1=Enabled,0=Disabled'],
        ]],
    ],
];
