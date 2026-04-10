<?php

declare(strict_types=1);

namespace App\UserBC\Presentation\Controllers;

use App\SharedKernel\Domain\ValueObjects\EmailVO;
use App\UserBC\Application\Commands\LoginCommand;
use App\UserBC\Application\UseCases\LoginUseCase;
use App\UserBC\Infrastructure\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuthController
{
    public function __construct(
        private readonly LoginUseCase $loginUseCase
    ) {}

    public function login(Request $request): JsonResponse
    {
        $data = $request->all();

        $email = EmailVO::create($data['email']);
        $command = new LoginCommand($email, $data['password']);

        $response = $this->loginUseCase->execute($command);

        $token = Auth::login(
            UserModel::where('email', $data['email'])->first()
        );

        return response()->json([
            'user' => $response->toArray(),
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json(['message' => 'Logout exitoso']);
    }

    public function me(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'enabled' => $user->enabled,
        ]);
    }
}
