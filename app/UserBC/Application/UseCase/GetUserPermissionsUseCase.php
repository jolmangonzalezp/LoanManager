<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\Exceptions\UserNotFoundException;
use App\UserBC\Domain\Repository\UserFinderById;
use App\UserBC\Domain\Repository\UserRoleFinder;
use App\UserBC\Domain\ValueObject\UserIdVO;

final readonly class GetUserPermissionsUseCase
{
    public function __construct(
        private UserFinderById $userFinder,
        private UserRoleFinder $roleFinder,
    ) {}

    public function execute(string $id): array
    {
        $userId = UserIdVO::fromString($id);
        $user = $this->userFinder->findById($userId);
        if ($user === null) {
            throw new UserNotFoundException;
        }

        return $this->roleFinder->findPermissionSlugs($id);
    }
}
