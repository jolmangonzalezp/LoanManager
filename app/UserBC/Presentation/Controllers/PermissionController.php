<?php

declare(strict_types=1);

namespace App\UserBC\Presentation\Controllers;

use App\UserBC\Application\CQRS\Command\CreatePermissionCommand;
use App\UserBC\Application\UseCase\CreatePermissionUseCase;
use App\UserBC\Application\UseCase\ListPermissionsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PermissionController
{
    public function __construct(
        private readonly ListPermissionsUseCase $listPermissionsUseCase,
        private readonly CreatePermissionUseCase $createPermissionUseCase,
    ) {}

    public function index(): JsonResponse
    {
        $permissions = $this->listPermissionsUseCase->execute();

        return response()->json($permissions);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'slug' => 'required|string',
            'name' => 'required|string',
            'description' => 'sometimes|nullable|string',
            'group' => 'sometimes|nullable|string',
        ]);

        $command = new CreatePermissionCommand(
            slug: $data['slug'],
            name: $data['name'],
            description: $data['description'] ?? null,
            group: $data['group'] ?? null,
        );

        $response = $this->createPermissionUseCase->execute($command);

        return response()->json($response->toArray(), 201);
    }
}
