<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\Permission;
use App\UserBC\Domain\Repository\PermissionCreator;
use App\UserBC\Infrastructure\Mapper\PermissionMapper;
use App\UserBC\Infrastructure\Persistence\Model\PermissionModel;

final class EloquentPermissionCreator implements PermissionCreator
{
    public function __construct(
        private readonly PermissionMapper $mapper,
    ) {}

    public function create(Permission $permission): void
    {
        PermissionModel::create($this->mapper->toPersistence($permission));
    }
}
