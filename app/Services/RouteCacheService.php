<?php

namespace App\Services;

use App\Dto\RouteCacheEntityDto;
use App\Enums\RouteCacheList;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class RouteCacheService
{
    public function get(RouteCacheEntityDto $dto): mixed
    {
        return Cache::get($dto->getCacheKey());
    }

    public function store(RouteCacheEntityDto $dto, mixed $data): mixed
    {
        return Cache::remember($dto->getCacheKey(), $dto->ttl, fn () => $data);
    }

    public function exists(RouteCacheEntityDto $dto): bool
    {
        return ! empty(Redis::connection('default')->keys($dto->getCacheKey()));
    }

    public function forget(string $model): void
    {
        if (! in_array($model, RouteCacheList::entityModels())) {
            return;
        }

        $cacheKeys = [];

        foreach (Redis::connections() as $connection) {
            $cacheKeys = [
                ...$cacheKeys,
                ...$connection->keys("*"),
            ];
        }

        $cacheKeys = collect($cacheKeys)->unique();

        foreach ($cacheKeys as $cacheKey) {
            if (str_contains($cacheKey, $model)) {
                Cache::forget(Str::after($cacheKey, Cache::getPrefix()));
            }
        }
    }

    public function forgetAll(): void
    {
        $entities = RouteCacheList::entities();
        $models = [];

        /** @var RouteCacheEntityDto $entity */
        foreach ($entities as $entity) {
            $models = [
                ...$models,
                ...$entity->getKeys(),
            ];
        }

        $models = array_unique($models);

        $cacheKeys = [];

        foreach (Redis::connections() as $connection) {
            $cacheKeys = [
                ...$cacheKeys,
                ...$connection->keys("*")
            ];
        }

        /** @var RouteCacheEntityDto $entity */
        foreach ($models as $model) {
            foreach ($cacheKeys as $cacheKey) {
                if (str_contains($cacheKey, $model)) {
                    Cache::forget(Str::after($cacheKey, Cache::getPrefix()));
                }
            }
        }
    }

    public function forgetByKey($key): void
    {
        $cacheKeys = [];

        foreach (Redis::connections() as $connection) {
            $cacheKeys = [
                ...$cacheKeys,
                ...$connection->keys("*"),
            ];
        }

        $cacheKeys = collect($cacheKeys)->unique();

        foreach ($cacheKeys as $cacheKey) {
            if (str_contains($cacheKey, $key)) {
                Cache::forget(Str::after($cacheKey, Cache::getPrefix()));
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function getAllRelations($model) : array
    {
        $reflect = new ReflectionClass($model);

        $relations = [];

        foreach($reflect->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (in_array($method->getReturnType(), [HasOne::class, HasMany::class, BelongsTo::class, BelongsToMany::class])) {
                $relations[] = $method->getName();
            }
        }

        return $relations;
    }
}
