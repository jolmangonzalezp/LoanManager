<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Repository\UserRoleFinder;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class EloquentUserRoleFinder implements UserRoleFinder
{
    public function findRoleSlugs(string $userId): array
    {
        $user = UserModel::with('roles')->findOrFail($userId);

        return $user->roles->pluck('slug')->toArray();
    }

    public function findPermissionSlugs(string $userId): array
    {
        $user = UserModel::with('roles.permissions')->findOrFail($userId);

        return $user->roles
            ->flatMap(fn ($role) => $role->permissions->pluck('slug'))
            ->unique()
            ->values()
            ->toArray();
    }
}
