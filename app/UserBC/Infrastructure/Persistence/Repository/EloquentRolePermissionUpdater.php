<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Repository\RolePermissionUpdater;
use App\UserBC\Infrastructure\Persistence\Model\PermissionModel;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;

final class EloquentRolePermissionUpdater implements RolePermissionUpdater
{
    public function syncPermissions(string $roleId, array $permissionSlugs): void
    {
        $role = RoleModel::findOrFail($roleId);
        $permissionIds = PermissionModel::whereIn('slug', $permissionSlugs)->pluck('id')->toArray();
        $role->permissions()->sync($permissionIds);
    }
}
