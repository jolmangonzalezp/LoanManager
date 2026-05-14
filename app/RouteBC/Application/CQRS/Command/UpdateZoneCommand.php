<?php

declare(strict_types=1);

namespace App\RouteBC\Application\CQRS\Command;

use App\RouteBC\Domain\ValueObject\ZoneIdVO;

final class UpdateZoneCommand
{
    public function __construct(
        public readonly ZoneIdVO $id,
        public readonly string $name,
        public readonly array $polygon,
    ) {}
}
