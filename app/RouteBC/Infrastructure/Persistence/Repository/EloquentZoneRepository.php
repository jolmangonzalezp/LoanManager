<?php

declare(strict_types=1);

namespace App\RouteBC\Infrastructure\Persistence\Repository;

use App\RouteBC\Domain\Aggregate\Zone;
use App\RouteBC\Domain\Repository\ZoneRepositoryInterface;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;
use App\RouteBC\Infrastructure\Mapper\ZoneMapper;
use App\RouteBC\Infrastructure\Persistence\Model\ZoneModel;

final class EloquentZoneRepository implements ZoneRepositoryInterface
{
    public function __construct(
        private readonly ZoneMapper $mapper
    ) {}

    public function save(Zone $zone): void
    {
        ZoneModel::updateOrCreate(
            ['id' => $zone->getId()->getValue()],
            $this->mapper->toPersistence($zone)
        );
    }

    public function findById(ZoneIdVO $id): ?Zone
    {
        $model = ZoneModel::find($id->getValue());

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findAll(): array
    {
        return ZoneModel::all()->map(fn (ZoneModel $m) => $this->mapper->toDomain($m))->toArray();
    }

    public function delete(ZoneIdVO $id): void
    {
        ZoneModel::where('id', $id->getValue())->delete();
    }

    public function findAllWithCoordinates(): array
    {
        return ZoneModel::all()->map(fn (ZoneModel $m) => $this->mapper->toDomain($m))->toArray();
    }
}
