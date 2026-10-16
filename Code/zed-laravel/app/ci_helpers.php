<?php

/*
 * The few CodeIgniter view helpers that the public-site templates (ported from
 * the old site with the same markup) still call. New views should use Blade.
 */

use Illuminate\Support\Str;

if (! function_exists('url_title')) {
    function url_title(string $str, string $separator = '-', bool $lowercase = true): string
    {
        return Str::slug($str, $separator);
    }
}

if (! function_exists('ci_attributes')) {
    /** @param array<string, string>|string $attributes */
    function ci_attributes(array|string $attributes): string
    {
        if (is_string($attributes)) {
            return $attributes === '' ? '' : ' '.trim($attributes);
        }
        $out = '';
        foreach ($attributes as $name => $value) {
            $out .= ' '.e($name).'="'.e($value).'"';
        }

        return $out;
    }
}

if (! function_exists('form_open')) {
    /** <form method="post"> with the CSRF token. */
    function form_open(string $action = '', array|string $attributes = [], array $hidden = []): string
    {
        $action = $action === '' ? url()->current() : (preg_match('#^https?://#', $action) ? $action : url($action));
        $html = '<form action="'.e($action).'" method="post" accept-charset="utf-8"'.ci_attributes($attributes).'>'."\n";
        $html .= '<input type="hidden" name="_token" value="'.e(csrf_token()).'">'."\n";
        foreach ($hidden as $name => $value) {
            $html .= '<input type="hidden" name="'.e($name).'" value="'.e($value).'">'."\n";
        }

        return $html;
    }
}

if (! function_exists('form_open_multipart')) {
    function form_open_multipart(string $action = '', array|string $attributes = [], array $hidden = []): string
    {
        $attributes = is_array($attributes) ? $attributes + ['enctype' => 'multipart/form-data'] : $attributes.' enctype="multipart/form-data"';

        return form_open($action, $attributes, $hidden);
    }
}

if (! function_exists('form_close')) {
    function form_close(string $extra = ''): string
    {
        return '</form>'.$extra;
    }
}

if (! function_exists('form_dropdown')) {
    /** @param array<int|string, string> $options */
    function form_dropdown(string $name, array $options = [], mixed $selected = [], array|string $extra = ''): string
    {
        $selected = array_map('strval', (array) $selected);
        $html = '<select name="'.e($name).'"'.ci_attributes($extra).">\n";
        foreach ($options as $value => $label) {
            $html .= '<option value="'.e($value).'"'.(in_array((string) $value, $selected, true) ? ' selected="selected"' : '').'>'.e($label)."</option>\n";
        }

        return $html."</select>\n";
    }
}
