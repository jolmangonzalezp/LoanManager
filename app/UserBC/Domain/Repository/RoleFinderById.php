<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Repository;

use App\UserBC\Domain\Aggregate\Role;
use App\UserBC\Domain\ValueObject\RoleIdVO;

interface RoleFinderById
{
    public function findById(RoleIdVO $id): ?Role;
}
