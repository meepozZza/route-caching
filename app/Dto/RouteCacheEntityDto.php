<?php

namespace App\Dto;

use Illuminate\Support\Arr;
use Spatie\LaravelData\Data;

class RouteCacheEntityDto extends Data
{
    public function __construct(
        public array $models = [],
        public int $ttl = 60 * 60,
        public bool $considerRequestForKeys = true,
        public bool $considerAuthUserForKeys = true,
    ) {
    }

    public function getKeys(): array
    {
        return [
            ...$this->models,
            ...$this->considerRequestForKeys ? ['request:'.Arr::query(request()->all())] : [],
            ...$this->considerAuthUserForKeys ? ['auth:'.auth()->id()] : []
        ];
    }

    public function getCacheKey(): string
    {
        return Arr::join($this->getKeys(), '|');
    }
}
