<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Config\Provider;

use App\UserBC\Application\UseCase\AssignUserRolesUseCase;
use App\UserBC\Application\UseCase\CreatePermissionUseCase;
use App\UserBC\Application\UseCase\CreateRoleUseCase;
use App\UserBC\Application\UseCase\CreateUserUseCase;
use App\UserBC\Application\UseCase\DeleteRoleUseCase;
use App\UserBC\Application\UseCase\GetRoleUseCase;
use App\UserBC\Application\UseCase\GetUserPermissionsUseCase;
use App\UserBC\Application\UseCase\GetUserUseCase;
use App\UserBC\Application\UseCase\ListPermissionsUseCase;
use App\UserBC\Application\UseCase\ListRolesUseCase;
use App\UserBC\Application\UseCase\ListUsersUseCase;
use App\UserBC\Application\UseCase\LoginUseCase;
use App\UserBC\Application\UseCase\RequestPasswordResetUseCase;
use App\UserBC\Application\UseCase\ResetPasswordUseCase;
use App\UserBC\Application\UseCase\UpdateRoleUseCase;
use App\UserBC\Application\UseCase\UpdateUserUseCase;
use App\UserBC\Domain\Repository\PermissionCreator;
use App\UserBC\Domain\Repository\PermissionDeleter;
use App\UserBC\Domain\Repository\PermissionFinderAll;
use App\UserBC\Domain\Repository\PermissionFinderBySlug;
use App\UserBC\Domain\Repository\RoleCreator;
use App\UserBC\Domain\Repository\RoleDeleter;
use App\UserBC\Domain\Repository\RoleFinderAll;
use App\UserBC\Domain\Repository\RoleFinderById;
use App\UserBC\Domain\Repository\RoleFinderBySlug;
use App\UserBC\Domain\Repository\RolePermissionUpdater;
use App\UserBC\Domain\Repository\RoleUpdater;
use App\UserBC\Domain\Repository\UserCreator;
use App\UserBC\Domain\Repository\UserFinderAll;
use App\UserBC\Domain\Repository\UserFinderByEmail;
use App\UserBC\Domain\Repository\UserFinderById;
use App\UserBC\Domain\Repository\UserFinderByUsername;
use App\UserBC\Domain\Repository\UserRoleAssigner;
use App\UserBC\Domain\Repository\UserRoleFinder;
use App\UserBC\Domain\Repository\UserUpdater;
use App\UserBC\Infrastructure\Mapper\PermissionMapper;
use App\UserBC\Infrastructure\Mapper\RoleMapper;
use App\UserBC\Infrastructure\Mapper\UserMapper;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentPermissionCreator;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentPermissionDeleter;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentPermissionFinderAll;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentPermissionFinderBySlug;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentRoleCreator;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentRoleDeleter;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentRoleFinderAll;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentRoleFinderById;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentRoleFinderBySlug;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentRolePermissionUpdater;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentRoleUpdater;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserCreator;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserFinderAll;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserFinderByEmail;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserFinderById;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserFinderByUsername;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserRoleAssigner;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserRoleFinder;
use App\UserBC\Infrastructure\Persistence\Repository\EloquentUserUpdater;
use App\UserBC\Presentation\Controllers\AuthController;
use App\UserBC\Presentation\Controllers\PermissionController;
use App\UserBC\Presentation\Controllers\RoleController;
use App\UserBC\Presentation\Controllers\UserController;
use App\UserBC\Presentation\Middleware\PermissionMiddleware;
use App\UserBC\Presentation\Middleware\RoleMiddleware;
use Illuminate\Support\ServiceProvider;

final class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserMapper::class);
        $this->app->singleton(PermissionMapper::class);
        $this->app->singleton(RoleMapper::class);

        // User Repositories
        $this->app->bind(UserCreator::class, EloquentUserCreator::class);
        $this->app->bind(UserFinderByEmail::class, EloquentUserFinderByEmail::class);
        $this->app->bind(UserFinderById::class, EloquentUserFinderById::class);
        $this->app->bind(UserFinderByUsername::class, EloquentUserFinderByUsername::class);
        $this->app->bind(UserFinderAll::class, EloquentUserFinderAll::class);
        $this->app->bind(UserUpdater::class, EloquentUserUpdater::class);

        // Permission Repositories
        $this->app->bind(PermissionCreator::class, EloquentPermissionCreator::class);
        $this->app->bind(PermissionFinderBySlug::class, EloquentPermissionFinderBySlug::class);
        $this->app->bind(PermissionFinderAll::class, EloquentPermissionFinderAll::class);
        $this->app->bind(PermissionDeleter::class, EloquentPermissionDeleter::class);

        // Role Repositories
        $this->app->bind(RoleCreator::class, EloquentRoleCreator::class);
        $this->app->bind(RoleFinderBySlug::class, EloquentRoleFinderBySlug::class);
        $this->app->bind(RoleFinderById::class, EloquentRoleFinderById::class);
        $this->app->bind(RoleFinderAll::class, EloquentRoleFinderAll::class);
        $this->app->bind(RoleUpdater::class, EloquentRoleUpdater::class);
        $this->app->bind(RoleDeleter::class, EloquentRoleDeleter::class);
        $this->app->bind(RolePermissionUpdater::class, EloquentRolePermissionUpdater::class);

        // User-Role Repositories
        $this->app->bind(UserRoleAssigner::class, EloquentUserRoleAssigner::class);
        $this->app->bind(UserRoleFinder::class, EloquentUserRoleFinder::class);

        // Auth Use Cases
        $this->app->singleton(LoginUseCase::class);
        $this->app->singleton(RequestPasswordResetUseCase::class);
        $this->app->singleton(ResetPasswordUseCase::class);

        // User Use Cases
        $this->app->singleton(GetUserUseCase::class);
        $this->app->singleton(CreateUserUseCase::class);
        $this->app->singleton(UpdateUserUseCase::class);
        $this->app->singleton(ListUsersUseCase::class);

        // Permission Use Cases
        $this->app->singleton(CreatePermissionUseCase::class);
        $this->app->singleton(ListPermissionsUseCase::class);

        // Role Use Cases
        $this->app->singleton(CreateRoleUseCase::class);
        $this->app->singleton(UpdateRoleUseCase::class);
        $this->app->singleton(ListRolesUseCase::class);
        $this->app->singleton(GetRoleUseCase::class);
        $this->app->singleton(DeleteRoleUseCase::class);

        // User-Role Use Cases
        $this->app->singleton(AssignUserRolesUseCase::class);
        $this->app->singleton(GetUserPermissionsUseCase::class);

        // Controllers
        $this->app->singleton(AuthController::class);
        $this->app->singleton(UserController::class);
        $this->app->singleton(PermissionController::class);
        $this->app->singleton(RoleController::class);

        // Middleware
        $this->app->singleton(PermissionMiddleware::class);
        $this->app->singleton(RoleMiddleware::class);
    }
}
