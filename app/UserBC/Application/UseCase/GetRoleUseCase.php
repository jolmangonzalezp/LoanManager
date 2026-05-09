<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\DTOs\RoleResponse;
use App\UserBC\Application\Exceptions\RoleNotFoundException;
use App\UserBC\Domain\Repository\RoleFinderById;
use App\UserBC\Domain\ValueObject\RoleIdVO;

final readonly class GetRoleUseCase
{
    public function __construct(
        private RoleFinderById $finder,
    ) {}

    public function execute(string $id): array
    {
        $roleId = RoleIdVO::fromString($id);
        $role = $this->finder->findById($roleId);
        if ($role === null) {
            throw new RoleNotFoundException;
        }

        return RoleResponse::fromEntity($role)->toArray();
    }
}
