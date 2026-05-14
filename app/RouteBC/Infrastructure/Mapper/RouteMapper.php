<?php

declare(strict_types=1);

namespace App\RouteBC\Infrastructure\Mapper;

use App\RouteBC\Domain\Aggregate\Route;
use App\RouteBC\Domain\ValueObject\RouteIdVO;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;
use App\RouteBC\Infrastructure\Persistence\Model\RouteModel;

final class RouteMapper
{
    public function toDomain(RouteModel $model): Route
    {
        $userIds = $model->relationLoaded('users')
            ? $model->users->pluck('id')->toArray()
            : [];

        return Route::reconstitute(
            RouteIdVO::fromString($model->id),
            $model->name,
            ZoneIdVO::fromString($model->zone_id),
            $userIds
        );
    }

    public function toPersistence(Route $route): array
    {
        return [
            'id' => $route->getId()->getValue(),
            'name' => $route->getName(),
            'zone_id' => $route->getZoneId()->getValue(),
        ];
    }
}
