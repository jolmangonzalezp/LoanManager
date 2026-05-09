<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\Exceptions\RoleCannotBeDeletedException;
use App\UserBC\Application\Exceptions\RoleNotFoundException;
use App\UserBC\Domain\Repository\RoleDeleter;
use App\UserBC\Domain\Repository\RoleFinderById;
use App\UserBC\Domain\ValueObject\RoleIdVO;

final readonly class DeleteRoleUseCase
{
    public function __construct(
        private RoleFinderById $finder,
        private RoleDeleter $deleter,
    ) {}

    public function execute(string $id): void
    {
        $roleId = RoleIdVO::fromString($id);
        $role = $this->finder->findById($roleId);
        if ($role === null) {
            throw new RoleNotFoundException;
        }

        if ($role->isSystem()) {
            throw new RoleCannotBeDeletedException;
        }

        $this->deleter->delete($roleId);
    }
}
