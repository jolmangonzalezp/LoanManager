<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Mapper;

use App\SharedKernel\Domain\ValueObject\DateVO;
use App\UserBC\Domain\Aggregate\Permission;
use App\UserBC\Domain\ValueObject\PermissionIdVO;
use App\UserBC\Infrastructure\Persistence\Model\PermissionModel;

final class PermissionMapper
{
    public function toDomain(PermissionModel $model): Permission
    {
        return Permission::reconstitute(
            id: PermissionIdVO::fromString($model->id),
            slug: $model->slug,
            name: $model->name,
            description: $model->description,
            group: $model->group,
            createdAt: DateVO::fromDateTime($model->created_at),
        );
    }

    public function toPersistence(Permission $permission): array
    {
        return [
            'id' => $permission->getId()->getValue(),
            'slug' => $permission->getSlug(),
            'name' => $permission->getName(),
            'description' => $permission->getDescription(),
            'group' => $permission->getGroup(),
        ];
    }
}
