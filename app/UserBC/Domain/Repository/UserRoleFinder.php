<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

interface UserRoleFinder
{
    /** @return string[] */
    public function findRoleSlugs(string $userId): array;

    /** @return string[] */
    public function findPermissionSlugs(string $userId): array;
}
