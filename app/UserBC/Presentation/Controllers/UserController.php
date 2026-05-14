<?php

declare(strict_types=1);

namespace App\UserBC\Presentation\Controllers;

use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;
use App\UserBC\Application\CQRS\Command\AssignUserRolesCommand;
use App\UserBC\Application\CQRS\Command\CreateUserCommand;
use App\UserBC\Application\CQRS\Command\UpdateUserCommand;
use App\UserBC\Application\UseCase\AssignUserRolesUseCase;
use App\UserBC\Application\UseCase\CreateUserUseCase;
use App\UserBC\Application\UseCase\GetUserPermissionsUseCase;
use App\UserBC\Application\UseCase\GetUserUseCase;
use App\UserBC\Application\UseCase\ListUsersUseCase;
use App\UserBC\Application\UseCase\UpdateUserUseCase;
use App\UserBC\Domain\Repository\UserRoleFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserController
{
    public function __construct(
        private readonly ListUsersUseCase $listUsersUseCase,
        private readonly GetUserUseCase $getUserUseCase,
        private readonly CreateUserUseCase $createUserUseCase,
        private readonly UpdateUserUseCase $updateUserUseCase,
        private readonly AssignUserRolesUseCase $assignUserRolesUseCase,
        private readonly GetUserPermissionsUseCase $getUserPermissionsUseCase,
        private readonly UserRoleFinder $roleFinder,
    ) {}

    public function index(): JsonResponse
    {
        $users = $this->listUsersUseCase->execute();

        return response()->json($users);
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->getUserUseCase->execute($id);

        return response()->json($user);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string|min:3',
            'password' => 'required|string|min:6',
            'name' => 'sometimes|nullable|array',
            'name.first_name' => 'required_with:name|string',
            'name.middle_name' => 'sometimes|nullable|string',
            'name.last_name' => 'sometimes|nullable|string',
            'name.second_last_name' => 'sometimes|nullable|string',
            'email' => 'sometimes|nullable|email',
            'phone' => 'sometimes|nullable|string',
        ]);

        $name = array_key_exists('name', $data) && $data['name']
            ? NameVO::create(
                $data['name']['first_name'] ?? '',
                !empty($data['name']['last_name']) ? $data['name']['last_name'] : 'Unknown',
                !empty($data['name']['second_last_name']) ? $data['name']['second_last_name'] : 'Unknown',
                $data['name']['middle_name'] ?? null,
            )
            : null;

        $email = array_key_exists('email', $data)
            ? ($data['email'] ? EmailVO::create($data['email']) : null)
            : null;

        $phone = array_key_exists('phone', $data)
            ? ($data['phone'] ? PhoneVO::create($data['phone']) : null)
            : null;

        $command = new CreateUserCommand(
            username: $data['username'],
            password: $data['password'],
            name: $name,
            email: $email,
            phone: $phone,
        );

        $response = $this->createUserUseCase->execute($command);

        return response()->json($response, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string|min:3',
            'name' => 'sometimes|nullable|array',
            'name.first_name' => 'required_with:name|string',
            'name.middle_name' => 'sometimes|nullable|string',
            'name.last_name' => 'sometimes|nullable|string',
            'name.second_last_name' => 'sometimes|nullable|string',
            'email' => 'sometimes|nullable|email',
            'phone' => 'sometimes|nullable|string',
            'enabled' => 'sometimes|boolean',
        ]);

        $name = array_key_exists('name', $data) && $data['name']
            ? NameVO::create(
                $data['name']['first_name'] ?? '',
                !empty($data['name']['last_name']) ? $data['name']['last_name'] : 'Unknown',
                !empty($data['name']['second_last_name']) ? $data['name']['second_last_name'] : 'Unknown',
                $data['name']['middle_name'] ?? null,
            )
            : null;

        $email = array_key_exists('email', $data)
            ? ($data['email'] ? EmailVO::create($data['email']) : null)
            : null;

        $phone = array_key_exists('phone', $data)
            ? ($data['phone'] ? PhoneVO::create($data['phone']) : null)
            : null;

        $command = new UpdateUserCommand(
            id: $id,
            username: $data['username'],
            name: $name,
            email: $email,
            phone: $phone,
            enabled: isset($data['enabled']) ? (bool) $data['enabled'] : null,
        );

        $response = $this->updateUserUseCase->execute($command);

        return response()->json($response);
    }

    public function destroy(string $id): JsonResponse
    {
        $command = new UpdateUserCommand(
            id: $id,
            username: '',
            enabled: false,
        );

        $this->updateUserUseCase->execute($command);

        return response()->json(['message' => 'Usuario deshabilitado']);
    }

    public function assignRoles(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string',
        ]);

        $command = new AssignUserRolesCommand(
            userId: $id,
            roleSlugs: $data['roles'],
        );

        $this->assignUserRolesUseCase->execute($command);

        return response()->json(['message' => 'Roles asignados exitosamente']);
    }

    public function roles(string $id): JsonResponse
    {
        $roles = $this->roleFinder->findRoleSlugs($id);

        return response()->json(['roles' => $roles]);
    }

    public function permissions(string $id): JsonResponse
    {
        $permissions = $this->getUserPermissionsUseCase->execute($id);

        return response()->json(['permissions' => $permissions]);
    }
}
