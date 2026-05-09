<?php

declare(strict_types=1);

namespace App\UserBC\Presentation\Controllers;

use App\UserBC\Application\CQRS\Command\CreateRoleCommand;
use App\UserBC\Application\CQRS\Command\UpdateRoleCommand;
use App\UserBC\Application\UseCase\CreateRoleUseCase;
use App\UserBC\Application\UseCase\DeleteRoleUseCase;
use App\UserBC\Application\UseCase\GetRoleUseCase;
use App\UserBC\Application\UseCase\ListRolesUseCase;
use App\UserBC\Application\UseCase\UpdateRoleUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RoleController
{
    public function __construct(
        private readonly ListRolesUseCase $listRolesUseCase,
        private readonly GetRoleUseCase $getRoleUseCase,
        private readonly CreateRoleUseCase $createRoleUseCase,
        private readonly UpdateRoleUseCase $updateRoleUseCase,
        private readonly DeleteRoleUseCase $deleteRoleUseCase,
    ) {}

    public function index(): JsonResponse
    {
        $roles = $this->listRolesUseCase->execute();

        return response()->json($roles);
    }

    public function show(string $id): JsonResponse
    {
        $role = $this->getRoleUseCase->execute($id);

        return response()->json($role);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'slug' => 'required|string',
            'name' => 'required|string',
            'description' => 'sometimes|nullable|string',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'string',
        ]);

        $command = new CreateRoleCommand(
            slug: $data['slug'],
            name: $data['name'],
            description: $data['description'] ?? null,
            permissions: $data['permissions'] ?? [],
        );

        $response = $this->createRoleUseCase->execute($command);

        return response()->json($response->toArray(), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'slug' => 'required|string',
            'name' => 'required|string',
            'description' => 'sometimes|nullable|string',
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'string',
        ]);

        $command = new UpdateRoleCommand(
            id: $id,
            slug: $data['slug'],
            name: $data['name'],
            description: $data['description'] ?? null,
            permissions: $data['permissions'] ?? null,
        );

        $response = $this->updateRoleUseCase->execute($command);

        return response()->json($response->toArray());
    }

    public function destroy(string $id): JsonResponse
    {
        $this->deleteRoleUseCase->execute($id);

        return response()->json(['message' => 'Rol eliminado']);
    }
}
