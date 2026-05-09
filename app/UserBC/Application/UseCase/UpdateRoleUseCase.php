<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\CQRS\Command\UpdateRoleCommand;
use App\UserBC\Application\DTOs\RoleResponse;
use App\UserBC\Application\Exceptions\RoleNotFoundException;
use App\UserBC\Application\Exceptions\RoleSlugAlreadyExistsException;
use App\UserBC\Domain\Aggregate\Role;
use App\UserBC\Domain\Repository\RoleFinderById;
use App\UserBC\Domain\Repository\RoleFinderBySlug;
use App\UserBC\Domain\Repository\RolePermissionUpdater;
use App\UserBC\Domain\Repository\RoleUpdater;
use App\UserBC\Domain\ValueObject\RoleIdVO;

final readonly class UpdateRoleUseCase
{
    public function __construct(
        private RoleFinderById $finder,
        private RoleFinderBySlug $slugFinder,
        private RoleUpdater $updater,
        private RolePermissionUpdater $permissionUpdater,
    ) {}

    public function execute(UpdateRoleCommand $command): RoleResponse
    {
        $roleId = RoleIdVO::fromString($command->id);
        $role = $this->finder->findById($roleId);
        if ($role === null) {
            throw new RoleNotFoundException;
        }

        if ($command->slug !== $role->getSlug()) {
            $existing = $this->slugFinder->findBySlug($command->slug);
            if ($existing !== null) {
                throw new RoleSlugAlreadyExistsException;
            }
        }

        $updated = Role::reconstitute(
            id: $roleId,
            slug: $command->slug,
            name: $command->name,
            description: $command->description,
            isSystem: $role->isSystem(),
            createdAt: $role->getCreatedAt(),
        );

        $this->updater->update($updated);

        if ($command->permissions !== null) {
            $this->permissionUpdater->syncPermissions($command->id, $command->permissions);
        }

        return RoleResponse::fromEntity($updated);
    }
}
