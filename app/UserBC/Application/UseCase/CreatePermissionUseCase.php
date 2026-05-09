<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\CQRS\Command\CreatePermissionCommand;
use App\UserBC\Application\DTOs\PermissionResponse;
use App\UserBC\Application\Exceptions\PermissionSlugAlreadyExistsException;
use App\UserBC\Domain\Aggregate\Permission;
use App\UserBC\Domain\Repository\PermissionCreator;
use App\UserBC\Domain\Repository\PermissionFinderBySlug;

final readonly class CreatePermissionUseCase
{
    public function __construct(
        private PermissionCreator $creator,
        private PermissionFinderBySlug $finder,
    ) {}

    public function execute(CreatePermissionCommand $command): PermissionResponse
    {
        $existing = $this->finder->findBySlug($command->slug);
        if ($existing !== null) {
            throw new PermissionSlugAlreadyExistsException;
        }

        $permission = Permission::create(
            slug: $command->slug,
            name: $command->name,
            description: $command->description,
            group: $command->group,
        );

        $this->creator->create($permission);

        return PermissionResponse::fromEntity($permission);
    }
}
