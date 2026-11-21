<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'key',
    'value',
    'type',
])]
class SiteSetting extends Model
{
    public const CACHE_KEY = 'site_settings.all';

    /**
     * @return array<string, string|null>
     */
    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            return static::query()
                ->pluck('value', 'key')
                ->map(fn ($value) => $value === null || $value === '' ? null : (string) $value)
                ->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::allCached();

        if (! array_key_exists($key, $settings)) {
            return $default;
        }

        return $settings[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $type = 'string'): self
    {
        $normalized = is_string($value) ? trim($value) : $value;

        if ($normalized === '') {
            $normalized = null;
        }

        $setting = static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $normalized,
                'type' => $type,
            ],
        );

        static::flushCache();

        return $setting;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::set($key, $value);
        }
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
