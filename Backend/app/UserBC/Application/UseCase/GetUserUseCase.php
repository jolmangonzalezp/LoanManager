<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\DTOs\UserResponse;
use App\UserBC\Application\Exceptions\UserNotFoundException;
use App\UserBC\Domain\Repository\UserFinderById;
use App\UserBC\Domain\ValueObject\UserIdVO;

final class GetUserUseCase
{
    public function __construct(
        private readonly UserFinderById $finder
    ) {}

    public function execute(string $id): array
    {
        $userId = UserIdVO::fromString($id);

        $user = $this->finder->findById($userId);

        if (! $user) {
            throw new UserNotFoundException;
        }

        return UserResponse::fromEntity($user)->toArray();
    }
}
