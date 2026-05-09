<?php

declare(strict_types=1);

namespace App\UserBC\Application\DTOs;

use App\UserBC\Domain\Aggregate\User;

final class UserResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly string $createdAt,
        public readonly bool $enabled,
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            $user->getId()->getValue(),
            $user->getUsername(),
            $user->getName()?->getFullName(),
            $user->getEmail()?->getValue(),
            $user->getPhone()?->getNumber(),
            $user->getCreatedAt()->getFormatted(),
            $user->isEnabled(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
        ];
    }
}
