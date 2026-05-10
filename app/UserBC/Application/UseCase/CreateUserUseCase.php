<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Application\CQRS\Command\CreateUserCommand;
use App\UserBC\Application\DTOs\CreatedUserResponse;
use App\UserBC\Application\Exceptions\UsernameAlreadyExistsException;
use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserCreator;
use App\UserBC\Domain\Repository\UserFinderByUsername;
use App\UserBC\Domain\Repository\UserRoleAssigner;

final class CreateUserUseCase
{
    private ?CreatedUserResponse $response = null;

    public function __construct(
        private UserCreator $creator,
        private UserFinderByUsername $userFinder,
        private UserRoleAssigner $roleAssigner,
    ) {}

    public function getResponse(): ?CreatedUserResponse
    {
        return $this->response;
    }

    public function execute(CreateUserCommand $command): bool
    {
        $existing = $this->userFinder->findByUsername($command->username);
        if ($existing !== null) {
            throw new UsernameAlreadyExistsException;
        }

        $user = User::create(
            username: $command->username,
            password: $command->password,
            name: $command->name,
            email: $command->email,
            phone: $command->phone,
        );

        $this->creator->create($user);
        $this->roleAssigner->assignRoles($user->getId()->getValue(), ['viewer']);

        $this->response = CreatedUserResponse::fromEntity($user);

        return true;
    }
}
