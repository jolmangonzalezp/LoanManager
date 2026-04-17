<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\UserBC\Application\Exceptions\InvalidCredentialsException;
use App\UserBC\Application\Exceptions\UserDisabledException;
use App\UserBC\Domain\Repository\UserFinderByEmail;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final class LoginUseCase
{
    public function __construct(
        private readonly UserFinderByEmail $finder
    ) {}

    public function execute(string $email, string $password): array
    {
        $emailVO = EmailVO::create($email);
        $user = $this->finder->findByEmail($emailVO);
        
        if (! $user || ! $user->verifyPassword($password)) {
            throw new InvalidCredentialsException;
        }

        if (! $user->isEnabled()) {
            throw new UserDisabledException;
        }

        $userModel = UserModel::find($user->getId()->getValue());
        $token = $userModel->createToken('auth-token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->getId()->getValue(),
                'name' => $user->getPersonalData()->getName()->getFullName(),
                'email' => $user->getPersonalData()->getEmail()?->getValue(),
            ],
            'token' => $token,
        ];
    }
}
