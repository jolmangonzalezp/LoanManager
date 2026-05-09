<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\Role;
use App\UserBC\Domain\Repository\RoleFinderById;
use App\UserBC\Domain\ValueObject\RoleIdVO;
use App\UserBC\Infrastructure\Mapper\RoleMapper;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;

final class EloquentRoleFinderById implements RoleFinderById
{
    public function __construct(
        private readonly RoleMapper $mapper,
    ) {}

    public function findById(RoleIdVO $id): ?Role
    {
        $model = RoleModel::where('id', $id->getValue())->first();

        return $model ? $this->mapper->toDomain($model) : null;
    }
}
