<?php

declare(strict_types=1);

namespace App\UserBC\Application\DTOs;

use App\UserBC\Domain\Aggregate\Role;

final class RoleResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $slug,
        public readonly string $name,
        public readonly ?string $description,
        public readonly bool $isSystem,
        public readonly string $createdAt,
    ) {}

    public static function fromEntity(Role $role): self
    {
        return new self(
            $role->getId()->getValue(),
            $role->getSlug(),
            $role->getName(),
            $role->getDescription(),
            $role->isSystem(),
            $role->getCreatedAt()->getFormatted(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'is_system' => $this->isSystem,
            'created_at' => $this->createdAt,
        ];
    }
}
