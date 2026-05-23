<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $guarded = [];

    public static function get(string $key, ?string $default = null): ?string
    {
        $cached = Cache::get("setting:{$key}", '__missing__');
        if ($cached !== '__missing__') {
            return $cached ?? $default;
        }

        $value = self::where('key', $key)->value('value');
        if ($value !== null) {
            Cache::forever("setting:{$key}", $value);
            return $value;
        }
        return $default;
    }

    public static function put(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting:{$key}");
    }

    public static function all_kv(): array
    {
        return self::query()->pluck('value', 'key')->toArray();
    }

    public static function flushCache(): void
    {
        foreach (self::pluck('key') as $k) {
            Cache::forget("setting:{$k}");
        }
    }
}
