<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCases;

use App\SharedKernel\Application\Exceptions\ForbiddenException;
use App\SharedKernel\Application\Exceptions\UnauthorizedException;
use App\UserBC\Application\Commands\LoginCommand;
use App\UserBC\Application\DTOs\UserResponse;
use App\UserBC\Domain\Repositories\UserFinderByEmail;

final class LoginUseCase
{
    public function __construct(
        private readonly UserFinderByEmail $finder
    ) {}

    public function execute(LoginCommand $command): UserResponse
    {
        $user = $this->finder->findByEmail($command->email);

        if ($user === null) {
            throw new UnauthorizedException('Credenciales inválidas');
        }

        if (! $user->isEnabled()) {
            throw new ForbiddenException('Usuario deshabilitado');
        }

        if (! $user->verifyPassword($command->password)) {
            throw new UnauthorizedException('Credenciales inválidas');
        }

        return UserResponse::fromEntity($user);
    }
}
