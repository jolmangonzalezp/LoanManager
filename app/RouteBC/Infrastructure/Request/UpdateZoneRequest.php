<?php

declare(strict_types=1);

namespace App\RouteBC\Infrastructure\Request;

use App\RouteBC\Application\CQRS\Command\UpdateZoneCommand;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;

final class UpdateZoneRequest
{
    public static function fromArray(string $id, array $data): UpdateZoneCommand
    {
        return new UpdateZoneCommand(
            id: ZoneIdVO::fromString($id),
            name: (string) ($data['name'] ?? ''),
            polygon: (array) ($data['polygon'] ?? []),
        );
    }
}
