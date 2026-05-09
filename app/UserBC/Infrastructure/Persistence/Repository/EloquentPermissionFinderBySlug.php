<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Persistence\Repository;

use App\UserBC\Domain\Aggregate\Permission;
use App\UserBC\Domain\Repository\PermissionFinderBySlug;
use App\UserBC\Infrastructure\Mapper\PermissionMapper;
use App\UserBC\Infrastructure\Persistence\Model\PermissionModel;

final class EloquentPermissionFinderBySlug implements PermissionFinderBySlug
{
    public function __construct(
        private readonly PermissionMapper $mapper,
    ) {}

    public function findBySlug(string $slug): ?Permission
    {
        $model = PermissionModel::where('slug', $slug)->first();

        return $model ? $this->mapper->toDomain($model) : null;
    }
}
