<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

interface RolePermissionUpdater
{
    /** @param string[] $permissionSlugs */
    public function syncPermissions(string $roleId, array $permissionSlugs): void;
}
