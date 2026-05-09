<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\Aggregate\Role;

interface RoleUpdater
{
    public function update(Role $role): void;
}
