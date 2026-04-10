<?php

declare(strict_types=1);

namespace App\UserBC\Application\DTOs;

use App\UserBC\Domain\Entities\User;

final class UserResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $enabled,
        public readonly bool $verified,
        public readonly string $createdAt
    ) {}

    public static function fromEntity(User $user): self
    {
        $personalData = $user->getPersonalData();
        $email = $personalData->getEmail();

        return new self(
            id: $user->getId()->getValue(),
            name: $personalData->getName()->getFullName(),
            email: $email ? (string) $email : '',
            enabled: $user->isEnabled(),
            verified: $user->isVerified(),
            createdAt: $user->getCreatedAt()->getFormatted('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'enabled' => $this->enabled,
            'verified' => $this->verified,
            'created_at' => $this->createdAt,
        ];
    }
}
