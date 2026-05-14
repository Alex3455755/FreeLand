<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    private const CACHE_PREFIX = 'site_setting:';

    public static function getValue(string $key, ?string $default = null): ?string
    {
        if (! Schema::hasTable('site_settings')) {
            return $default;
        }

        $cacheKey = self::CACHE_PREFIX.$key;

        return Cache::remember($cacheKey, 60, function () use ($key, $default) {
            $row = self::query()->where('key', $key)->first();

            return $row?->value ?? $default;
        });
    }

    public static function getFloatPercent(string $key, float $defaultPercent): float
    {
        $raw = self::getValue((string) $key, (string) $defaultPercent);
        $v = is_numeric($raw) ? (float) $raw : $defaultPercent;

        return max(0.0, min(100.0, $v));
    }

    public static function setValue(string $key, string $value): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        Cache::forget(self::CACHE_PREFIX.$key);
    }

    public static function commissionMultiplier(string $key, float $defaultPercent): float
    {
        return self::getFloatPercent($key, $defaultPercent) / 100.0;
    }
}
