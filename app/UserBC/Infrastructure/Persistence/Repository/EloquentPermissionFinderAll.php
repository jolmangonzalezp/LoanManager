<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\Permission;
use App\UserBC\Domain\Repository\PermissionFinderAll;
use App\UserBC\Infrastructure\Mapper\PermissionMapper;
use App\UserBC\Infrastructure\Persistence\Model\PermissionModel;

final class EloquentPermissionFinderAll implements PermissionFinderAll
{
    public function __construct(
        private readonly PermissionMapper $mapper,
    ) {}

    public function findAll(): array
    {
        return PermissionModel::all()
            ->map(fn (PermissionModel $model) => $this->mapper->toDomain($model))
            ->toArray();
    }
}
