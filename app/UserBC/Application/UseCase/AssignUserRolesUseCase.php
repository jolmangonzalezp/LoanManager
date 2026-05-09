<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\CQRS\Command\AssignUserRolesCommand;
use App\UserBC\Application\Exceptions\UserNotFoundException;
use App\UserBC\Domain\Repository\UserFinderById;
use App\UserBC\Domain\Repository\UserRoleAssigner;
use App\UserBC\Domain\ValueObject\UserIdVO;

final readonly class AssignUserRolesUseCase
{
    public function __construct(
        private UserFinderById $userFinder,
        private UserRoleAssigner $roleAssigner,
    ) {}

    public function execute(AssignUserRolesCommand $command): void
    {
        $userId = UserIdVO::fromString($command->userId);
        $user = $this->userFinder->findById($userId);
        if ($user === null) {
            throw new UserNotFoundException;
        }

        $this->roleAssigner->assignRoles($command->userId, $command->roleSlugs);
    }
}
