<?php

declare(strict_types=1);

namespace App\UserBC\Application\CQRS\Command;

final readonly class UpdateRoleCommand
{
    public function __construct(
        public string $id,
        public string $slug,
        public string $name,
        public ?string $description = null,
        public ?array $permissions = null,
    ) {}
}
