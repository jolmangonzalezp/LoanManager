<?php

namespace App\SharedKernel\Presentation\Middleware;

use App\SharedKernel\Presentation\Mappers\ErrorMapper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HandleExceptions
{
    private const RATE_LIMIT = 60;
    private const RATE_WINDOW = 60;

    private static array $rateLimits = [];

    public function handle(Request $request, Closure $next): Response
    {
        $this->logRequest($request);

        if (!$this->checkAuth($request)) {
            return $this->unauthorized();
        }

        if (!$this->checkRateLimit($request)) {
            return $this->rateLimited();
        }

        try {
            return $next($request);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    private function logRequest(Request $request): void
    {
        try {
            Log::info('API Request', [
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);
        } catch (\Throwable) {
            // Silently ignore logging errors
        }
    }

    private function checkAuth(Request $request): bool
    {
        if (env('APP_ENV') === 'local') {
            return true;
        }

        $publicRoutes = ['api/auth/login', 'api/auth/me'];

        if (in_array($request->path(), $publicRoutes)) {
            return true;
        }

        if ($request->bearerToken()) {
            return true;
        }

        return false;
    }

    private function checkRateLimit(Request $request): bool
    {
        $ip = $request->ip();
        $now = time();

        if (!isset(self::$rateLimits[$ip])) {
            self::$rateLimits[$ip] = [];
        }

        self::$rateLimits[$ip] = array_filter(
            self::$rateLimits[$ip],
            fn($timestamp) => $timestamp > $now - self::RATE_WINDOW
        );

        if (count(self::$rateLimits[$ip]) >= self::RATE_LIMIT) {
            return false;
        }

        self::$rateLimits[$ip][] = $now;

        return true;
    }

    private function handleException(\Throwable $e): Response
    {
        return ErrorMapper::map($e);
    }

    private function unauthorized(): Response
    {
        return new Response(
            json_encode(['message' => 'Unauthorized']),
            401,
            ['Content-Type' => 'application/json']
        );
    }

    private function rateLimited(): Response
    {
        return new Response(
            json_encode(['message' => 'Too many requests']),
            429,
            ['Content-Type' => 'application/json']
        );
    }
}
