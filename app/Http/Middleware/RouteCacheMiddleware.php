<?php

namespace App\Http\Middleware;

use App\Enums\RouteCacheList;
use App\Services\RouteCacheService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class RouteCacheMiddleware
{
    public function __construct(private RouteCacheService $responseCacheService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route()->getName();

        /** @var ?RouteCacheList $entity */
        $entity = RouteCacheList::tryFrom($route);

        if (! $entity) {
            return $next($request);
        }

        if (! $this->responseCacheService->exists($entity->entity())) {
            $this->responseCacheService->store($entity->entity(), $next($request));
        }

        return $this->responseCacheService->get($entity->entity());
    }
}
