<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

interface UserRoleAssigner
{
    /** @param string[] $roleSlugs */
    public function assignRoles(string $userId, array $roleSlugs): void;

    public function removeAllRoles(string $userId): void;
}
