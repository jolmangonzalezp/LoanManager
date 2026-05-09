<?php

declare(strict_types=1);

namespace App\UserBC\Application\UseCase;

use App\UserBC\Infrastructure\Persistence\Model\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

final readonly class ResetPasswordUseCase
{
    public function execute(string $token, string $email, string $password, string $passwordConfirmation): array
    {
        $status = Password::reset(
            [
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
                'token' => $token,
            ],
            function (UserModel $user, string $password): void {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete();
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return ['message' => 'Contrasena restablecida exitosamente.'];
        }

        if ($status === Password::INVALID_TOKEN) {
            return ['message' => 'El token de recuperacion es invalido o ha expirado.'];
        }

        return ['message' => 'No se pudo restablecer la contrasena.'];
    }
}
