<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\DTOs\RoleResponse;
use App\UserBC\Domain\Repository\RoleFinderAll;

final readonly class ListRolesUseCase
{
    public function __construct(
        private RoleFinderAll $finder,
    ) {}

    public function execute(): array
    {
        $roles = $this->finder->findAll();

        return array_map(
            fn ($role) => RoleResponse::fromEntity($role)->toArray(),
            $roles,
        );
    }
}
