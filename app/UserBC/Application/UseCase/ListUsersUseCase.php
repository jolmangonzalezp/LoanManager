<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\DTOs\UserResponse;
use App\UserBC\Domain\Repository\UserFinderAll;

final readonly class ListUsersUseCase
{
    public function __construct(
        private UserFinderAll $finder,
    ) {}

    public function execute(): array
    {
        $users = $this->finder->findAll();

        return array_map(
            fn ($user) => UserResponse::fromEntity($user)->toArray(),
            $users,
        );
    }
}
