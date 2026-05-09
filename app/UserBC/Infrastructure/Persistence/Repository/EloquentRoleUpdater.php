<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\Role;
use App\UserBC\Domain\Repository\RoleUpdater;
use App\UserBC\Infrastructure\Mapper\RoleMapper;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;

final class EloquentRoleUpdater implements RoleUpdater
{
    public function __construct(
        private readonly RoleMapper $mapper,
    ) {}

    public function update(Role $role): void
    {
        RoleModel::where('id', $role->getId()->getValue())
            ->update($this->mapper->toPersistence($role));
    }
}
