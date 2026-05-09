<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\Role;
use App\UserBC\Domain\Repository\RoleFinderAll;
use App\UserBC\Infrastructure\Mapper\RoleMapper;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;

final class EloquentRoleFinderAll implements RoleFinderAll
{
    public function __construct(
        private readonly RoleMapper $mapper,
    ) {}

    public function findAll(): array
    {
        return RoleModel::all()
            ->map(fn (RoleModel $model) => $this->mapper->toDomain($model))
            ->toArray();
    }
}
