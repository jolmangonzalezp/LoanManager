<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\UserBC\Application\CQRS\Command\CreateUserCommand;
use App\UserBC\Application\DTOs\CreatedUserResponse;
use App\UserBC\Application\Exceptions\UserAlreadyExistsException;
use App\UserBC\Domain\Aggregate\User;
use App\UserBC\Domain\Repository\UserCreator;
use App\UserBC\Domain\Repository\UserFinderByEmail;

final class CreateUserUseCase
{
    public function __construct(
        private readonly UserCreator $creator,
        private readonly UserFinderByEmail $finder
    ) {}

    public function execute(CreateUserCommand $command): CreatedUserResponse
    {
        $personalData = $command->personalData;
        $email = $personalData->getEmail();

        if ($email !== null) {
            $emailVO = EmailVO::create($email->getValue());
            $existing = $this->finder->findByEmail($emailVO);
            if ($existing !== null) {
                throw new UserAlreadyExistsException;
            }
        }

        $user = User::create($personalData, $command->password);

        $this->creator->create($user);

        return CreatedUserResponse::fromEntity($user);
    }
}
