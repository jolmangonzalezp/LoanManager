<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\UserBC\Application\Exceptions\InvalidCredentialsException;
use App\UserBC\Application\Exceptions\UserDisabledException;
use App\UserBC\Domain\Repository\UserFinderByEmail;
use App\UserBC\Domain\Repository\UserFinderByUsername;
use App\UserBC\Domain\Repository\UserRoleFinder;
use App\UserBC\Infrastructure\Persistence\Model\UserModel;

final readonly class LoginUseCase
{
    public function __construct(
        private UserFinderByUsername $userFinderByUsername,
        private UserFinderByEmail $userFinderByEmail,
        private UserRoleFinder $roleFinder,
    ) {}

    public function execute(string $login, string $password): array
    {
        $user = $this->userFinderByUsername->findByUsername($login);

        if ($user === null) {
            try {
                $emailVO = EmailVO::create($login);
                $user = $this->userFinderByEmail->findByEmail($emailVO);
            } catch (\Throwable) {
                $user = null;
            }
        }

        if ($user === null || !$user->verifyPassword($password)) {
            throw new InvalidCredentialsException;
        }

        if (!$user->isEnabled()) {
            throw new UserDisabledException;
        }

        $userModel = UserModel::find($user->getId()->getValue());
        $token = $userModel->createToken('auth-token')->plainTextToken;

        $userId = $user->getId()->getValue();
        $roles = $this->roleFinder->findRoleSlugs($userId);

        $name = $user->getName();

        return [
            'user' => [
                'id' => $userId,
                'username' => $user->getUsername(),
                'name' => $name ? [
                    'first_name' => $name->getFirstName(),
                    'middle_name' => $name->getMiddleName(),
                    'last_name' => $name->getLastName(),
                    'second_last_name' => $name->getSecondLastName(),
                ] : null,
                'email' => $user->getEmail()?->getValue(),
                'roles' => $roles,
            ],
            'token' => $token,
        ];
    }
}
