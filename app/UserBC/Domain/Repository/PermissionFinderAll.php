<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\Aggregate\Permission;

interface PermissionFinderAll
{
    /** @return Permission[] */
    public function findAll(): array;
}
