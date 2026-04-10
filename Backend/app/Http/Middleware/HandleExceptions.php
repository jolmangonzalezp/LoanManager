<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\SharedKernel\Presentation\Exceptions\ExceptionMapper;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class HandleExceptions
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            $mapper = app(ExceptionMapper::class);

            return $mapper->map($e, $request);
        }
    }
}
