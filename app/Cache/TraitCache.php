<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

trait TraitCache
{
    static function getNameCache(): string
    {
        return 'tabla_' . self::TABLE_NAME;
    }

    static function clearCache(): void
    {
        $cacheName = self::getNameCache();
        Cache::forget($cacheName);
    }
}
