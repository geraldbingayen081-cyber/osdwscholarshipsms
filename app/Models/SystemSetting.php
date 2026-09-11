<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Retrieve a setting value by key with fallback.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting && $setting->value !== null ? $setting->value : $default;
        });
    }

    /**
     * Store or update a setting value.
     */
    public static function set(string $key, $value): static
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("system_setting_{$key}");
        Cache::forget("system_settings_all");

        return $setting;
    }

    /**
     * Retrieve all settings as a key-value associative array.
     */
    public static function allSettings(): array
    {
        return Cache::remember('system_settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get the public URL of the custom system logo, or null if using default vector seal.
     */
    public static function logoUrl(): ?string
    {
        $path = static::get('system_logo_path');
        if ($path && Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        return null;
    }
}
