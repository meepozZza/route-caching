<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Services\RouteCacheService;

trait HasRouteCache
{
    public static function bootHasRouteCache(): void
    {
        static::callEventsForClearRouteModelCache();
    }

    public static function callEventsForClearRouteModelCache(): void
    {
        static::saved(function () {
            static::clearRouteModelCache();
        });

        static::deleted(function () {
            static::clearRouteModelCache();
        });
    }

    public static function clearRouteModelCache(): void
    {
        app(RouteCacheService::class)->forget(static::class);
    }
}
