<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\Aggregate\Permission;

interface PermissionFinderBySlug
{
    public function findBySlug(string $slug): ?Permission;
}
