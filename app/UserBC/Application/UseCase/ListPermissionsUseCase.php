<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\DTOs\PermissionResponse;
use App\UserBC\Domain\Repository\PermissionFinderAll;

final readonly class ListPermissionsUseCase
{
    public function __construct(
        private PermissionFinderAll $finder,
    ) {}

    public function execute(): array
    {
        $permissions = $this->finder->findAll();

        return array_map(
            fn ($permission) => PermissionResponse::fromEntity($permission)->toArray(),
            $permissions,
        );
    }
}
