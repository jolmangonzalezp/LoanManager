<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\Role;
use App\UserBC\Domain\Repository\RoleFinderBySlug;
use App\UserBC\Infrastructure\Mapper\RoleMapper;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;

final class EloquentRoleFinderBySlug implements RoleFinderBySlug
{
    public function __construct(
        private readonly RoleMapper $mapper,
    ) {}

    public function findBySlug(string $slug): ?Role
    {
        $model = RoleModel::where('slug', $slug)->first();

        return $model ? $this->mapper->toDomain($model) : null;
    }
}
