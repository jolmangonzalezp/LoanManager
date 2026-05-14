<?php

declare(strict_types=1);

namespace App\RouteBC\Presenter\Controllers;

use App\RouteBC\Application\CQRS\Command\AssignUsersToRouteCommand;
use App\RouteBC\Application\CQRS\Command\CreateRouteCommand;
use App\RouteBC\Application\DTO\RouteResponse;
use App\RouteBC\Application\UseCase\AssignUsersToRouteUseCase;
use App\RouteBC\Application\UseCase\CreateRouteUseCase;
use App\RouteBC\Application\UseCase\GetMapDataUseCase;
use App\RouteBC\Domain\Aggregate\Route;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;
use App\RouteBC\Domain\ValueObject\RouteIdVO;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class RouteController
{
    public function __construct(
        private CreateRouteUseCase $createRouteUseCase,
        private AssignUsersToRouteUseCase $assignUsersToRouteUseCase,
        private GetMapDataUseCase $getMapDataUseCase,
        private RouteRepositoryInterface $routeRepo,
    ) {}

    public function index(): JsonResponse
    {
        $routes = $this->routeRepo->findAll();
        $data = array_map(fn ($r) => RouteResponse::fromEntity($r)->toArray(), $routes);

        return response()->json($data);
    }

    public function show(string $id): JsonResponse
    {
        $route = $this->routeRepo->findById(RouteIdVO::fromString($id));
        if (! $route) {
            return response()->json(['error' => 'Route not found'], 404);
        }

        return response()->json(RouteResponse::fromEntity($route)->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $command = new CreateRouteCommand(
            name: (string) $request->input('name'),
            zoneId: ZoneIdVO::fromString((string) $request->input('zone_id')),
        );
        $success = $this->createRouteUseCase->execute($command);

        return response()->json($success ? $this->createRouteUseCase->getResponse() : false, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $route = $this->routeRepo->findById(RouteIdVO::fromString($id));
        if (! $route) {
            return response()->json(['error' => 'Route not found'], 404);
        }

        $zoneId = $request->input('zone_id', $route->getZoneId()->getValue());

        $updated = Route::reconstitute(
            RouteIdVO::fromString($id),
            (string) $request->input('name', $route->getName()),
            ZoneIdVO::fromString($zoneId),
            $route->getUserIds()
        );

        $this->routeRepo->save($updated);

        return response()->json(RouteResponse::fromEntity($updated)->toArray());
    }

    public function destroy(string $id): JsonResponse
    {
        $this->routeRepo->delete(RouteIdVO::fromString($id));

        return response()->json(true);
    }

    public function assignUsers(Request $request, string $id): JsonResponse
    {
        $command = new AssignUsersToRouteCommand(
            routeId: RouteIdVO::fromString($id),
            userIds: (array) $request->input('user_ids', []),
        );
        $success = $this->assignUsersToRouteUseCase->execute($command);

        return response()->json($success ? $this->assignUsersToRouteUseCase->getResponse() : false);
    }

    public function removeUser(string $id, string $userId): JsonResponse
    {
        $this->routeRepo->removeUser(RouteIdVO::fromString($id), $userId);

        return response()->json(true);
    }

    public function mapData(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = $user?->roles?->pluck('slug')?->contains('admin') ? 'admin' : 'agent';

        $success = $this->getMapDataUseCase->execute(
            userId: $user?->getAuthIdentifier(),
            role: $role
        );

        return response()->json($success ? $this->getMapDataUseCase->getResponse() : []);
    }
}
