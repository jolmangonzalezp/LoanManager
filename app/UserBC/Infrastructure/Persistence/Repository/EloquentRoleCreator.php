<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\Role;
use App\UserBC\Domain\Repository\RoleCreator;
use App\UserBC\Infrastructure\Mapper\RoleMapper;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;

final class EloquentRoleCreator implements RoleCreator
{
    public function __construct(
        private readonly RoleMapper $mapper,
    ) {}

    public function create(Role $role): void
    {
        RoleModel::create($this->mapper->toPersistence($role));
    }
}
