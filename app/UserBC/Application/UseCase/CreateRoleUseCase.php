<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\CQRS\Command\CreateRoleCommand;
use App\UserBC\Application\DTOs\RoleResponse;
use App\UserBC\Application\Exceptions\RoleSlugAlreadyExistsException;
use App\UserBC\Domain\Aggregate\Role;
use App\UserBC\Domain\Repository\RoleCreator;
use App\UserBC\Domain\Repository\RoleFinderBySlug;
use App\UserBC\Domain\Repository\RolePermissionUpdater;

final readonly class CreateRoleUseCase
{
    public function __construct(
        private RoleCreator $creator,
        private RoleFinderBySlug $finder,
        private RolePermissionUpdater $permissionUpdater,
    ) {}

    public function execute(CreateRoleCommand $command): RoleResponse
    {
        $existing = $this->finder->findBySlug($command->slug);
        if ($existing !== null) {
            throw new RoleSlugAlreadyExistsException;
        }

        $role = Role::create(
            slug: $command->slug,
            name: $command->name,
            description: $command->description,
            isSystem: false,
        );

        $this->creator->create($role);

        if (!empty($command->permissions)) {
            $this->permissionUpdater->syncPermissions($role->getId()->getValue(), $command->permissions);
        }

        return RoleResponse::fromEntity($role);
    }
}
