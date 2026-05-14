<?php

declare(strict_types=1);

namespace App\RouteBC\Application\DTO;

use App\RouteBC\Domain\Aggregate\Route;

final class RouteResponse
{
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $zoneId,
        public readonly array $userIds = [],
    ) {}

    public static function fromEntity(Route $route): self
    {
        return new self(
            id: $route->getId()->getValue(),
            name: $route->getName(),
            zoneId: $route->getZoneId()->getValue(),
            userIds: $route->getUserIds(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'zone_id' => $this->zoneId,
            'user_ids' => $this->userIds,
        ];
    }
}
