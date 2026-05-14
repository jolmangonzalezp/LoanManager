<?php

declare(strict_types=1);

namespace App\RouteBC\Domain\Aggregate;

use App\RouteBC\Domain\ValueObject\RouteIdVO;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;

final readonly class Route
{
    /** @param string[] $userIds */
    private function __construct(
        private RouteIdVO $id,
        private string $name,
        private ZoneIdVO $zoneId,
        private array $userIds = [],
    ) {}

    public static function create(
        RouteIdVO $id,
        string $name,
        ZoneIdVO $zoneId,
    ): self {
        return new self($id, $name, $zoneId);
    }

    public static function reconstitute(
        RouteIdVO $id,
        string $name,
        ZoneIdVO $zoneId,
        array $userIds = [],
    ): self {
        return new self($id, $name, $zoneId, $userIds);
    }

    public function getId(): RouteIdVO
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getZoneId(): ZoneIdVO
    {
        return $this->zoneId;
    }

    public function getUserIds(): array
    {
        return $this->userIds;
    }
}
