<?php

declare(strict_types=1);

namespace App\UserBC\Application\DTOs;

use App\UserBC\Domain\Aggregate\Permission;

final class PermissionResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $slug,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $group,
        public readonly string $createdAt,
    ) {}

    public static function fromEntity(Permission $permission): self
    {
        return new self(
            $permission->getId()->getValue(),
            $permission->getSlug(),
            $permission->getName(),
            $permission->getDescription(),
            $permission->getGroup(),
            $permission->getCreatedAt()->getFormatted(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'group' => $this->group,
            'created_at' => $this->createdAt,
        ];
    }
}
