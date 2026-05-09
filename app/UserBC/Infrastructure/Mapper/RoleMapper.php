<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Mapper;

use App\SharedKernel\Domain\ValueObject\DateVO;
use App\UserBC\Domain\Aggregate\Role;
use App\UserBC\Domain\ValueObject\RoleIdVO;
use App\UserBC\Infrastructure\Persistence\Model\RoleModel;

final class RoleMapper
{
    public function toDomain(RoleModel $model): Role
    {
        return Role::reconstitute(
            id: RoleIdVO::fromString($model->id),
            slug: $model->slug,
            name: $model->name,
            description: $model->description,
            isSystem: (bool) $model->is_system,
            createdAt: DateVO::fromDateTime($model->created_at),
        );
    }

    public function toPersistence(Role $role): array
    {
        return [
            'id' => $role->getId()->getValue(),
            'slug' => $role->getSlug(),
            'name' => $role->getName(),
            'description' => $role->getDescription(),
            'is_system' => $role->isSystem(),
        ];
    }
}
