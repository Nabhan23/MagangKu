<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil nilai setting (dengan cache). Fallback ke $default bila belum disimpan.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Simpan satu atau banyak setting sekaligus: ['key' => 'value', ...].
     */
    public static function set(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value !== null ? (string) $value : null]);
            Cache::forget("setting.{$key}");
        }
    }
}
