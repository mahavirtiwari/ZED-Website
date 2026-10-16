<?php

/*
 * Extra Content-Security-Policy sources, added to the strict defaults in
 * App\Http\Middleware\SecurityHeaders (self + per-request nonce).
 * Never add 'unsafe-inline' or 'unsafe-eval' (security audit, item 4).
 */
return [
    'csp' => [
        // Zoho Desk contact form
        'script-src' => ['https://js.zohostatic.in'],
        'style-src' => ['https://fonts.googleapis.com', 'https://js.zohostatic.in'],
        'font-src' => ['https://fonts.gstatic.com'],
        'connect-src' => ['https://desk.zoho.in'],
        // content images can come from other sites; uploads preview as blob:
        'img-src' => ['https:', 'blob:'],
        // embedded videos, the MSME dashboard and maps
        'frame-src' => ['https://www.youtube.com', 'https://www.youtube-nocookie.com', 'https://msmekawach.org.in', 'https://www.google.com', 'https://desk.zoho.in'],
        'form-action' => ['https://desk.zoho.in'],
    ],
];
