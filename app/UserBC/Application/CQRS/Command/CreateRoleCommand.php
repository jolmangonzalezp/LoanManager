<?php

declare(strict_types=1);

namespace App\UserBC\Application\CQRS\Command;

final readonly class CreateRoleCommand
{
    public function __construct(
        public string $slug,
        public string $name,
        public ?string $description = null,
        public array $permissions = [],
    ) {}
}
