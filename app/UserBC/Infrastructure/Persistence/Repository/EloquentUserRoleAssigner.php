<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Repository\UserRoleAssigner;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class EloquentUserRoleAssigner implements UserRoleAssigner
{
    public function assignRoles(string $userId, array $roleSlugs): void
    {
        $user = UserModel::findOrFail($userId);
        $roleIds = RoleModel::whereIn('slug', $roleSlugs)->pluck('id')->toArray();
        $user->roles()->sync($roleIds);
    }

    public function removeAllRoles(string $userId): void
    {
        $user = UserModel::findOrFail($userId);
        $user->roles()->sync([]);
    }
}
