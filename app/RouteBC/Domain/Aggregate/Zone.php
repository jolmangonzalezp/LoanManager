<?php

declare(strict_types=1);

namespace App\RouteBC\Domain\Aggregate;

use App\RouteBC\Domain\ValueObject\Polygon;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;

final readonly class Zone
{
    private function __construct(
        private ZoneIdVO $id,
        private string $name,
        private Polygon $polygon,
    ) {}

    public static function create(
        ZoneIdVO $id,
        string $name,
        Polygon $polygon,
    ): self {
        return new self($id, $name, $polygon);
    }

    public static function reconstitute(
        ZoneIdVO $id,
        string $name,
        Polygon $polygon,
    ): self {
        return new self($id, $name, $polygon);
    }

    public function getId(): ZoneIdVO
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPolygon(): Polygon
    {
        return $this->polygon;
    }

    public function withName(string $name): self
    {
        return new self($this->id, $name, $this->polygon);
    }

    public function withPolygon(Polygon $polygon): self
    {
        return new self($this->id, $this->name, $polygon);
    }
}
