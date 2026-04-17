<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Config\Provider;

use App\UserBC\Application\UseCase\CreateUserUseCase;
use App\UserBC\Application\UseCase\GetUserUseCase;
use App\UserBC\Application\UseCase\LoginUseCase;
use App\UserBC\Domain\Repository\UserCreator;
use App\UserBC\Domain\Repository\UserFinderByEmail;
use App\UserBC\Domain\Repository\UserFinderById;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserCreator;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserFinderByEmail;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserFinderById;
use App\UserBC\Infrastructure\Mapper\UserMapper;
use App\UserBC\Presenter\Controllers\AuthController;
use Illuminate\Support\ServiceProvider;

final class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserMapper::class);
        $this->app->bind(UserCreator::class, EloquentUserCreator::class);
        $this->app->bind(UserFinderByEmail::class, EloquentUserFinderByEmail::class);
        $this->app->bind(UserFinderById::class, EloquentUserFinderById::class);

        $this->app->singleton(LoginUseCase::class);
        $this->app->singleton(GetUserUseCase::class);
        $this->app->singleton(CreateUserUseCase::class);

        $this->app->singleton(AuthController::class);
    }
}
