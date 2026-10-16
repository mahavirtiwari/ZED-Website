<?php

return [
    /*
     * Folder that holds the uploaded files (uploads/, photogallery/, slider/).
     * After the switch-over this is the Laravel public/ folder. While the old
     * CodeIgniter site still runs, point it at the old web root so both share
     * the same files (ZED_STORAGE_ROOT=../zedcms in .env).
     */
    'storage_root' => env('ZED_STORAGE_ROOT') ? realpath(base_path(env('ZED_STORAGE_ROOT'))) ?: env('ZED_STORAGE_ROOT') : public_path(),

    /*
     * Old hostnames that appear inside stored image/file URLs (and in editor
     * HTML). media_url() turns them into paths on this site.
     */
    'legacy_hosts' => [
        'zed.msme.gov.in', 'www.zed.msme.gov.in', 'zed.org.in', 'zedv2.umon.in', 'zedv3.umon.in',
        'zed-admin.retedys.com', 'zed-web2.retedys.com', 'zed-staging.qci.org.in',
    ],

    // Remote APIs used by the public site
    'dashboard_counts_url' => env('ZED_DASHBOARD_COUNTS_URL', 'https://loginzed.msme.gov.in/dashboard/getcounts'),
    'verify_url' => env('ZED_VERIFY_URL', 'https://login.zed.org.in/msme/GetDetailsByUdyam'),
    'verify_token' => env('ZED_VERIFY_TOKEN'),

    // Contact Us (website form) recipients
    'contact_to' => env('ZED_CONTACT_TO', 'zedtp@qcin.org'),
    'contact_cc' => env('ZED_CONTACT_CC', 'zed@qcin.org'),
    'contact_training_to' => env('ZED_CONTACT_TRAINING_TO', 'zed@qcin.org'),
    'contact_categories' => ['ZED Certification', 'Registration / Login', 'Payment', 'Training Program', 'Awareness Program', 'Complaint', 'Other'],
];
