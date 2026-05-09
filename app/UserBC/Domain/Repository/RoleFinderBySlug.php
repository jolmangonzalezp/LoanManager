<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\Aggregate\Role;

interface RoleFinderBySlug
{
    public function findBySlug(string $slug): ?Role;
}
