<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Site settings as label / value rows (zed_global_config): SEO texts, counters,
 * contact address, security switches. Read them with zed_setting().
 */
class GlobalConfig extends Model
{
    protected $table = 'zed_global_config';

    public $timestamps = false;

    protected $fillable = ['label', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('zed.settings'));
        static::deleted(fn () => Cache::forget('zed.settings'));
    }

    /** Save several settings (insert missing labels). */
    public static function put(array $values): void
    {
        foreach ($values as $label => $value) {
            static::query()->updateOrCreate(['label' => $label], ['value' => (string) $value]);
        }
        Cache::forget('zed.settings');
    }
}
