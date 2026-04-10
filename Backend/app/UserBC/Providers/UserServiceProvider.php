<?php

declare(strict_types=1);

namespace App\UserBC\Providers;

use App\UserBC\Application\UseCases\LoginUseCase;
use App\UserBC\Domain\Repositories\UserCreator;
use App\UserBC\Domain\Repositories\UserFinderByEmail;
use App\UserBC\Infrastructure\Repositories\EloquentUserCreator;
use App\UserBC\Infrastructure\Repositories\EloquentUserFinderByEmail;
use App\UserBC\Infrastructure\Repositories\UserMapper;
use App\UserBC\Presentation\Controllers\AuthController;
use Illuminate\Support\ServiceProvider;

final class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserMapper::class);
        $this->app->bind(UserCreator::class, EloquentUserCreator::class);
        $this->app->bind(UserFinderByEmail::class, EloquentUserFinderByEmail::class);

        $this->app->bind(LoginUseCase::class);

        $this->app->singleton(AuthController::class);
    }
}
