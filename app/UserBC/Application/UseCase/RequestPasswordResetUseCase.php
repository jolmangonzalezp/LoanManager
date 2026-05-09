<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\UserBC\Domain\Repository\UserFinderByEmail;
use App\UserBC\Domain\Repository\UserFinderByUsername;
use Illuminate\Support\Facades\Password;

final readonly class RequestPasswordResetUseCase
{
    public function __construct(
        private UserFinderByUsername $userFinderByUsername,
        private UserFinderByEmail $userFinderByEmail,
    ) {}

    public function execute(string $login): array
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

        if ($user === null || $user->getEmail() === null) {
            return ['message' => 'Si el usuario existe, recibira un correo de recuperacion.'];
        }

        Password::sendResetLink(['email' => $user->getEmail()->getValue()]);

        return ['message' => 'Si el usuario existe, recibira un correo de recuperacion.'];
    }
}
