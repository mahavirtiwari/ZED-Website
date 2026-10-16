<?php

use App\Models\GlobalConfig;
use Illuminate\Support\Facades\Cache;

if (! function_exists('zed_setting')) {
    /**
     * A value from zed_global_config (CMS > Settings / Security), cached for a minute (settings saved by the old CodeIgniter CMS show up
     * within that time).
     * The cache is cleared whenever a setting is saved (GlobalConfig model events).
     */
    function zed_setting(string $label, ?string $default = null): ?string
    {
        $all = Cache::remember('zed.settings', 60, fn () => GlobalConfig::query()->pluck('value', 'label')->all());

        return array_key_exists($label, $all) ? $all[$label] : $default;
    }
}

if (! function_exists('captcha_enabled')) {
    /** Captcha switch (CMS > Security). On unless the setting is '0'. */
    function captcha_enabled(string $label): bool
    {
        return zed_setting($label, '1') !== '0';
    }
}

if (! function_exists('media_url')) {
    /**
     * URL for a stored image/file path. Stored values come in several forms:
     * absolute URLs with old hostnames (http://zedv2.umon.in/uploads/x.jpg),
     * Windows-style "host\\slider/x.jpg", "uploads/x.jpg" and "/uploads/x.jpg".
     * Known old hosts become paths on this site; other external URLs are kept.
     */
    function media_url(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        $value = str_replace('\\', '/', $value);
        if (preg_match('#^https?://([^/]+)(/.*)?$#i', $value, $m)) {
            $host = strtolower($m[1]);
            if (! in_array($host, config('zed.legacy_hosts'), true) && $host !== parse_url(config('app.url'), PHP_URL_HOST)) {
                return $value;
            }
            $value = $m[2] ?? '/';
        }

        return url('/'.ltrim($value, '/'));
    }
}

if (! function_exists('content_html')) {
    /**
     * Editor HTML from the database, with image/link URLs on old hostnames
     * rewritten to this site. The HTML itself is trusted CMS content.
     */
    function content_html(?string $html): string
    {
        $html = (string) $html;
        $hosts = implode('|', array_map('preg_quote', config('zed.legacy_hosts')));

        return preg_replace_callback('#(src|href)=(["\'])https?://(?:'.$hosts.')(/[^"\']*)?\2#i', function ($m) {
            return $m[1].'='.$m[2].url($m[3] ?? '/').$m[2];
        }, $html);
    }
}

if (! function_exists('base_url')) {
    /** Site root URL with a trailing slash (kept for templates ported from CodeIgniter). */
    function base_url(string $path = ''): string
    {
        return rtrim(url('/'), '/').'/'.ltrim($path, '/');
    }
}
