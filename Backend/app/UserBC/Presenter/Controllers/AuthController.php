<?php

declare(strict_types=1);

namespace App\UserBC\Presenter\Controllers;

use App\UserBC\Application\UseCase\LoginUseCase;
use App\UserBC\Application\UseCase\GetUserUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController
{
    public function __construct(
        private readonly LoginUseCase $loginUseCase,
        private readonly GetUserUseCase $getUserUseCase
    ) {}

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $result = $this->loginUseCase->execute($data['email'], $data['password']);

        return response()->json($result);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout exitoso']);
    }

    public function me(Request $request): JsonResponse
    {
        $result = $this->getUserUseCase->execute($request->user()->id);

        return response()->json($result);
    }
}
