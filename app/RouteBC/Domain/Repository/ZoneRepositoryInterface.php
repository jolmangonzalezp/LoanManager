<?php

declare(strict_types=1);

namespace App\RouteBC\Domain\Repository;

use App\RouteBC\Domain\Aggregate\Zone;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;

interface ZoneRepositoryInterface
{
    public function save(Zone $zone): void;

    public function findById(ZoneIdVO $id): ?Zone;

    /** @return Zone[] */
    public function findAll(): array;

    public function delete(ZoneIdVO $id): void;

    /** @return Zone[] */
    public function findAllWithCoordinates(): array;
}
