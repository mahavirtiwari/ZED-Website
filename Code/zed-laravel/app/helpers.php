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

if (! function_exists('setting_default')) {
    /** Built-in value of a setting (config/cms.php "settings"), shown until it is saved in the CMS. */
    function setting_default(string $label): string
    {
        static $defaults = null;
        if ($defaults === null) {
            $defaults = [];
            foreach (config('cms.settings') as [, , $fields]) {
                foreach ($fields as $name => $field) {
                    $defaults[$name] = (string) ($field[2] ?? '');
                }
            }
        }

        return $defaults[$label] ?? '';
    }
}

if (! function_exists('site_text')) {
    /**
     * Text set in CMS > Settings. Never saved: the built-in default. Saved empty:
     * '' (templates hide the item).
     */
    function site_text(string $label): string
    {
        return zed_setting($label) ?? setting_default($label);
    }
}

if (! function_exists('site_flag')) {
    /** Show/hide switch from CMS > Settings. */
    function site_flag(string $label): bool
    {
        $value = zed_setting($label);

        return ($value === null || $value === '' ? setting_default($label) : $value) !== '0';
    }
}

if (! function_exists('site_image')) {
    /** Image from CMS > Settings, or the built-in image. */
    function site_image(string $label): string
    {
        $value = (string) zed_setting($label);

        return $value !== '' ? media_url($value) : asset(setting_default($label));
    }
}

if (! function_exists('site_url')) {
    /** Link from CMS > Settings: absolute URLs as they are, "/page" relative to the site. */
    function site_url(string $label): string
    {
        $value = trim(site_text($label));

        return $value === '' || preg_match('#^https?://#i', $value) ? $value : url($value);
    }
}

if (! function_exists('youtube_embed_url')) {
    /** youtube.com/watch?v=ID or youtu.be/ID -> youtube.com/embed/ID (embed URLs unchanged). */
    function youtube_embed_url(?string $url): string
    {
        $url = trim((string) $url);
        if (preg_match('#(?:youtube\.com/watch\?(?:.*&)?v=|youtu\.be/)([A-Za-z0-9_-]{6,})#', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        return $url;
    }
}

if (! function_exists('zed_http')) {
    /** HTTP client for the ZED portal APIs (short timeouts; optional CA file, config zed.http_ca_file). */
    function zed_http(): \Illuminate\Http\Client\PendingRequest
    {
        $client = \Illuminate\Support\Facades\Http::connectTimeout(3)->timeout(10)->acceptJson();
        $caFile = (string) config('zed.http_ca_file');
        if ($caFile !== '') {
            $client = $client->withOptions(['verify' => realpath(base_path($caFile)) ?: $caFile]);
        }

        return $client;
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
