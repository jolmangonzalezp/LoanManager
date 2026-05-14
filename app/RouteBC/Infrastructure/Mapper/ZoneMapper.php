<?php

declare(strict_types=1);

namespace App\RouteBC\Infrastructure\Mapper;

use App\RouteBC\Domain\Aggregate\Zone;
use App\RouteBC\Domain\ValueObject\Polygon;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;
use App\RouteBC\Infrastructure\Persistence\Model\ZoneModel;

final class ZoneMapper
{
    public function toDomain(ZoneModel $model): Zone
    {
        return Zone::reconstitute(
            ZoneIdVO::fromString($model->id),
            $model->name,
            Polygon::fromArray($model->polygon)
        );
    }

    public function toPersistence(Zone $zone): array
    {
        return [
            'id' => $zone->getId()->getValue(),
            'name' => $zone->getName(),
            'polygon' => $zone->getPolygon()->toArray(),
        ];
    }
}
