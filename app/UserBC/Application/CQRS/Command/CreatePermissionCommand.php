<?php

declare(strict_types=1);

namespace App\UserBC\Application\CQRS\Command;

final readonly class CreatePermissionCommand
{
    public function __construct(
        public string $slug,
        public string $name,
        public ?string $description = null,
        public ?string $group = null,
    ) {}
}
