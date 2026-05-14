<?php

declare(strict_types=1);

namespace App\RouteBC\Application\CQRS\Command;

use App\RouteBC\Domain\ValueObject\ZoneIdVO;

final class CreateRouteCommand
{
    public function __construct(
        public readonly string $name,
        public readonly ZoneIdVO $zoneId,
    ) {}
}
