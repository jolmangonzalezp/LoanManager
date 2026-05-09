<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\CQRS\Command\UpdateUserCommand;
use App\UserBC\Application\DTOs\UserResponse;
use App\UserBC\Application\Exceptions\UserNotFoundException;
use App\UserBC\Domain\Repository\UserFinderById;
use App\UserBC\Domain\Repository\UserUpdater;

final readonly class UpdateUserUseCase
{
    public function __construct(
        private UserFinderById $finder,
        private UserUpdater $updater,
    ) {}

    public function execute(UpdateUserCommand $command): UserResponse
    {
        $userId = \App\UserBC\Domain\ValueObject\UserIdVO::fromString($command->id);
        $user = $this->finder->findById($userId);

        if ($user === null) {
            throw new UserNotFoundException;
        }

        $updated = \App\UserBC\Domain\Aggregate\User::reconstitute(
            id: $userId,
            username: $command->username,
            password: $user->getPassword(),
            enabled: $command->enabled ?? $user->isEnabled(),
            createdAt: $user->getCreatedAt(),
            name: $command->name ?? $user->getName(),
            email: $command->email ?? $user->getEmail(),
            phone: $command->phone ?? $user->getPhone(),
            rememberToken: $user->getRememberToken(),
        );

        $this->updater->update($updated);

        return UserResponse::fromEntity($updated);
    }
}
