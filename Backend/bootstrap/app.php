<?php

use App\SharedKernel\Presentation\Middleware\HandleExceptions;
use App\SharedKernel\Presentation\Middleware\HandleCors;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\SharedKernel\Presentation\Mappers\ErrorMapper;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'handle.exceptions' => HandleExceptions::class,
            'handle.cors' => HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e) {
            return ErrorMapper::map($e);
        });
    })->create();
