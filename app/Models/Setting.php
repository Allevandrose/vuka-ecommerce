<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Cache key used for all settings. Bump if the schema of `value` changes.
     */
    public const CACHE_KEY = 'settings.all';

    // ============================================
    // STATIC API — the way you should always read settings
    // ============================================

    /**
     * Get a setting value, cast to its declared type.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();

        if (!array_key_exists($key, $all)) {
            return $default;
        }

        return static::castValue($all[$key]['value'], $all[$key]['type']);
    }

    /**
     * Set a setting value and flush cache.
     */
    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        $encoded = match ($type) {
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'bool' => $value ? '1' : '0',
            default => (string) $value,
        };

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $encoded, 'type' => $type]
        );

        Cache::forget(static::CACHE_KEY);
    }

    /**
     * Remove a setting and flush cache.
     */
    public static function forget(string $key): void
    {
        static::where('key', $key)->delete();
        Cache::forget(static::CACHE_KEY);
    }

    /**
     * Reload cache from DB (call after bulk updates).
     */
    public static function refreshCache(): void
    {
        Cache::forget(static::CACHE_KEY);
        static::allCached();
    }

    // ============================================
    // INTERNALS
    // ============================================

    /**
     * Load all settings once, cached for 1 hour.
     * Returns an array: ['key' => ['value' => 'raw', 'type' => 'string'], ...]
     */
    protected static function allCached(): array
    {
        return Cache::remember(static::CACHE_KEY, 3600, function () {
            return static::query()
                ->get(['key', 'value', 'type'])
                ->keyBy('key')
                ->map(fn($s) => ['value' => $s->value, 'type' => $s->type])
                ->all();
        });
    }

    /**
     * Cast a stored string value to the declared type.
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'int' => (int) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    // ============================================
    // BOOT — flush cache on any write
    // ============================================

    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget(static::CACHE_KEY));
        static::deleted(fn() => Cache::forget(static::CACHE_KEY));
    }
}
