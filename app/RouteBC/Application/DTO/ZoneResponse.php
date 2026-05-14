<?php

declare(strict_types=1);

namespace App\RouteBC\Application\DTO;

use App\RouteBC\Domain\Aggregate\Zone;

final class ZoneResponse
{
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly array $polygon,
    ) {}

    public static function fromEntity(Zone $zone): self
    {
        return new self(
            id: $zone->getId()->getValue(),
            name: $zone->getName(),
            polygon: $zone->getPolygon()->toArray(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'polygon' => $this->polygon,
        ];
    }
}
