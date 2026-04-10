<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCases;

use App\SharedKernel\Application\Exceptions\ConflictException;
use App\UserBC\Application\Commands\CreateUserCommand;
use App\UserBC\Application\DTOs\UserResponse;
use App\UserBC\Domain\Entities\User;
use App\UserBC\Domain\Repositories\UserCreator;
use App\UserBC\Domain\Repositories\UserFinderByEmail;

final class CreateUserUseCase
{
    public function __construct(
        private readonly UserCreator $creator,
        private readonly UserFinderByEmail $finder
    ) {}

    public function execute(CreateUserCommand $command): UserResponse
    {
        $personalData = $command->personalData;
        $email = $personalData->getEmail();

        if ($email !== null) {
            $existing = $this->finder->findByEmail($email);
            if ($existing !== null) {
                throw new ConflictException('El email ya está registrado');
            }
        }

        $user = User::create(
            $personalData,
            $command->password
        );

        $this->creator->create($user);

        return UserResponse::fromEntity($user);
    }
}
