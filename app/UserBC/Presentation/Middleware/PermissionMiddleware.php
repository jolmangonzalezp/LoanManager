<?php

declare(strict_types=1);

namespace App\UserBC\Presentation\Middleware;

use App\UserBC\Application\UseCase\GetUserPermissionsUseCase;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PermissionMiddleware
{
    public function __construct(
        private readonly GetUserPermissionsUseCase $getUserPermissionsUseCase,
    ) {}

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $userId = $request->user()?->getAuthIdentifier();
        if ($userId === null) {
            return new Response(
                json_encode(['message' => 'Unauthenticated']),
                401,
                ['Content-Type' => 'application/json'],
            );
        }

        $permissions = $this->getUserPermissionsUseCase->execute($userId);
        if (!in_array($permission, $permissions, true)) {
            return new Response(
                json_encode(['message' => 'Forbidden']),
                403,
                ['Content-Type' => 'application/json'],
            );
        }

        return $next($request);
    }
}
