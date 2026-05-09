<?php

declare(strict_types=1);

namespace App\UserBC\Presentation\Controllers;

use App\UserBC\Application\UseCase\GetUserPermissionsUseCase;
use App\UserBC\Application\UseCase\GetUserUseCase;
use App\UserBC\Application\UseCase\LoginUseCase;
use App\UserBC\Application\UseCase\RequestPasswordResetUseCase;
use App\UserBC\Application\UseCase\ResetPasswordUseCase;
use App\UserBC\Domain\Repository\UserRoleFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController
{
    public function __construct(
        private readonly LoginUseCase $loginUseCase,
        private readonly GetUserUseCase $getUserUseCase,
        private readonly RequestPasswordResetUseCase $requestPasswordResetUseCase,
        private readonly ResetPasswordUseCase $resetPasswordUseCase,
        private readonly UserRoleFinder $roleFinder,
        private readonly GetUserPermissionsUseCase $getUserPermissionsUseCase,
    ) {}

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $result = $this->loginUseCase->execute($data['login'], $data['password']);

        return response()->json($result);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout exitoso']);
    }

    public function me(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $result = $this->getUserUseCase->execute($userId);
        $result['roles'] = $this->roleFinder->findRoleSlugs($userId);
        $result['permissions'] = $this->getUserPermissionsUseCase->execute($userId);

        return response()->json($result);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'login' => 'required|string',
        ]);

        $result = $this->requestPasswordResetUseCase->execute($data['login']);

        return response()->json($result);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $result = $this->resetPasswordUseCase->execute(
            $data['token'],
            $data['email'],
            $data['password'],
            $data['password'], // already confirmed by validation
        );

        return response()->json($result);
    }
}
