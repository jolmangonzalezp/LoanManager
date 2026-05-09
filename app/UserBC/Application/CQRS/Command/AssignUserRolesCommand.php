<?php

declare(strict_types=1);

namespace App\UserBC\Application\CQRS\Command;

final readonly class AssignUserRolesCommand
{
    public function __construct(
        public string $userId,
        public array $roleSlugs,
    ) {}
}
