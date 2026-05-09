<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Aggregate;

use App\SharedKernel\Domain\Exception\DomainException;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\UserBC\Domain\ValueObject\PermissionIdVO;

final class Permission
{
    private function __construct(
        private readonly PermissionIdVO $id,
        private readonly string $slug,
        private readonly string $name,
        private readonly ?string $description,
        private readonly ?string $group,
        private readonly DateVO $createdAt,
    ) {}

    public static function create(
        string $slug,
        string $name,
        ?string $description = null,
        ?string $group = null,
    ): self {
        $slug = trim(strtolower($slug));

        if ($slug === '' || !preg_match('/^[a-z]+\.[a-z_]+$/', $slug)) {
            throw new DomainException('invalid_permission_slug', 'El slug del permiso debe tener el formato "grupo.accion"');
        }

        if (empty($name)) {
            throw new DomainException('invalid_permission_name', 'El nombre del permiso es requerido');
        }

        return new self(
            PermissionIdVO::generate(),
            $slug,
            $name,
            $description,
            $group,
            DateVO::now(),
        );
    }

    public static function reconstitute(
        PermissionIdVO $id,
        string $slug,
        string $name,
        ?string $description,
        ?string $group,
        DateVO $createdAt,
    ): self {
        return new self($id, $slug, $name, $description, $group, $createdAt);
    }

    public function getId(): PermissionIdVO { return $this->id; }
    public function getSlug(): string { return $this->slug; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getGroup(): ?string { return $this->group; }
    public function getCreatedAt(): DateVO { return $this->createdAt; }
}
