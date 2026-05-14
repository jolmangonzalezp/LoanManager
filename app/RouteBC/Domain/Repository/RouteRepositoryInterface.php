<?php

declare(strict_types=1);

namespace App\RouteBC\Domain\Repository;

use App\RouteBC\Domain\Aggregate\Route;
use App\RouteBC\Domain\ValueObject\RouteIdVO;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;

interface RouteRepositoryInterface
{
    public function save(Route $route): void;

    public function findById(RouteIdVO $id): ?Route;

    /** @return Route[] */
    public function findAll(): array;

    /** @return Route[] */
    public function findByZoneId(ZoneIdVO $zoneId): array;

    /** @return Route[] */
    public function findByUserId(string $userId): array;

    public function delete(RouteIdVO $id): void;

    public function assignUsers(RouteIdVO $routeId, array $userIds): void;

    public function removeUser(RouteIdVO $routeId, string $userId): void;
}
