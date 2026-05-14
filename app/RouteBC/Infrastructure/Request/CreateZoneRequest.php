<?php

declare(strict_types=1);

namespace App\RouteBC\Infrastructure\Request;

use App\RouteBC\Application\CQRS\Command\CreateZoneCommand;

final class CreateZoneRequest
{
    public static function fromArray(array $data): CreateZoneCommand
    {
        return new CreateZoneCommand(
            name: (string) ($data['name'] ?? ''),
            polygon: (array) ($data['polygon'] ?? []),
        );
    }
}
