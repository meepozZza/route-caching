<?php

declare(strict_types=1);

namespace App\Enums;

use App\Dto\RouteCacheEntityDto;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Post;

enum RouteCacheList: string
{
    case API_POSTS_INDEX = 'api.posts.index';
    case API_ORDERS_INDEX = 'api.orders.index';

    public function entity(): RouteCacheEntityDto
    {
        return match ($this) {
            self::API_POSTS_INDEX => new RouteCacheEntityDto(
                [
                    Post::class,
                    Comment::class
                ],
            ),
            self::API_ORDERS_INDEX => new RouteCacheEntityDto(
                [
                    Order::class,
                ],
            ),
        };
    }

    public static function entities(): array
    {
        $entities = [];

        foreach (self::cases() as $case) {
            $entities[] = $case->entity();
        }

        return $entities;
    }

    public static function entityModels(): array
    {
        $models = [];

        foreach (self::cases() as $case) {
            $models = [
                ...$models,
                ...$case->entity()->models,
            ];
        }

        return collect($models)->unique()->toArray();
    }
}
