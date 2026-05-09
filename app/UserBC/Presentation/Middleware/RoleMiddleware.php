<?php

declare(strict_types=1);

namespace App\UserBC\Presentation\Middleware;

use App\UserBC\Domain\Repository\UserRoleFinder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RoleMiddleware
{
    public function __construct(
        private readonly UserRoleFinder $roleFinder,
    ) {}

    public function handle(Request $request, Closure $next, string $role): Response
    {
        $userId = $request->user()?->getAuthIdentifier();
        if ($userId === null) {
            return new Response(
                json_encode(['message' => 'Unauthenticated']),
                401,
                ['Content-Type' => 'application/json'],
            );
        }

        $roles = $this->roleFinder->findRoleSlugs($userId);
        if (!in_array($role, $roles, true)) {
            return new Response(
                json_encode(['message' => 'Forbidden']),
                403,
                ['Content-Type' => 'application/json'],
            );
        }

        return $next($request);
    }
}
