<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Aggregate;

use App\SharedKernel\Domain\Exception\DomainException;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\UserBC\Domain\ValueObject\RoleIdVO;

final class Role
{
    private function __construct(
        private readonly RoleIdVO $id,
        private readonly string $slug,
        private readonly string $name,
        private readonly ?string $description,
        private readonly bool $isSystem,
        private readonly DateVO $createdAt,
    ) {}

    public static function create(
        string $slug,
        string $name,
        ?string $description = null,
        bool $isSystem = false,
    ): self {
        $slug = trim(strtolower($slug));

        if ($slug === '' || !preg_match('/^[a-z_]+$/', $slug)) {
            throw new DomainException('invalid_role_slug', 'El slug del rol solo puede contener letras minúsculas y guiones bajos');
        }

        if (empty($name)) {
            throw new DomainException('invalid_role_name', 'El nombre del rol es requerido');
        }

        return new self(
            RoleIdVO::generate(),
            $slug,
            $name,
            $description,
            $isSystem,
            DateVO::now(),
        );
    }

    public static function reconstitute(
        RoleIdVO $id,
        string $slug,
        string $name,
        ?string $description,
        bool $isSystem,
        DateVO $createdAt,
    ): self {
        return new self($id, $slug, $name, $description, $isSystem, $createdAt);
    }

    public function getId(): RoleIdVO { return $this->id; }
    public function getSlug(): string { return $this->slug; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function isSystem(): bool { return $this->isSystem; }
    public function getCreatedAt(): DateVO { return $this->createdAt; }
}
