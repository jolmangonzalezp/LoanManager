<?php

declare(strict_types=1);

namespace App\RouteBC\Application\CQRS\Command;

use App\RouteBC\Domain\ValueObject\RouteIdVO;

final class AssignUsersToRouteCommand
{
    public function __construct(
        public readonly RouteIdVO $routeId,
        public readonly array $userIds,
    ) {}
}
