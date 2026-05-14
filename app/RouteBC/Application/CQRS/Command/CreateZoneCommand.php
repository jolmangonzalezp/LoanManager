<?php

declare(strict_types=1);

namespace App\RouteBC\Application\CQRS\Command;

final class CreateZoneCommand
{
    public function __construct(
        public readonly string $name,
        public readonly array $polygon,
    ) {}
}
