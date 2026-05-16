<?php

declare(strict_types=1);

namespace App\RouteBC\Infrastructure\Persistence\Repository;

use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\RouteBC\Domain\Aggregate\Route;
use App\RouteBC\Domain\Repository\RouteRepositoryInterface;
use App\RouteBC\Domain\ValueObject\RouteIdVO;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;
use App\RouteBC\Infrastructure\Mapper\RouteMapper;
use App\RouteBC\Infrastructure\Persistence\Model\RouteModel;
use Illuminate\Support\Facades\DB;

final class EloquentRouteRepository implements RouteRepositoryInterface
{
    public function __construct(
        private readonly RouteMapper $mapper
    ) {}

    public function save(Route $route): void
    {
        RouteModel::updateOrCreate(
            ['id' => $route->getId()->getValue()],
            $this->mapper->toPersistence($route)
        );
    }

    public function findById(RouteIdVO $id): ?Route
    {
        $model = RouteModel::with('users')->find($id->getValue());

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findAll(): array
    {
        return RouteModel::with('users')->get()
            ->map(fn (RouteModel $m) => $this->mapper->toDomain($m))
            ->toArray();
    }

    public function findByZoneId(ZoneIdVO $zoneId): array
    {
        return RouteModel::with('users')
            ->where('zone_id', $zoneId->getValue())
            ->get()
            ->map(fn (RouteModel $m) => $this->mapper->toDomain($m))
            ->toArray();
    }

    public function findByUserId(string $userId): array
    {
        return RouteModel::with('users')
            ->whereHas('users', fn ($q) => $q->where('user_id', $userId))
            ->get()
            ->map(fn (RouteModel $m) => $this->mapper->toDomain($m))
            ->toArray();
    }

    public function delete(RouteIdVO $id): void
    {
        DB::table('route_user')->where('route_id', $id->getValue())->delete();
        CustomerModel::where('route_id', $id->getValue())->update(['route_id' => null]);
        RouteModel::where('id', $id->getValue())->delete();
    }

    public function assignUsers(RouteIdVO $routeId, array $userIds): void
    {
        $route = RouteModel::find($routeId->getValue());
        if ($route) {
            $route->users()->sync($userIds);
        }
    }

    public function removeUser(RouteIdVO $routeId, string $userId): void
    {
        DB::table('route_user')
            ->where('route_id', $routeId->getValue())
            ->where('user_id', $userId)
            ->delete();
    }
}
