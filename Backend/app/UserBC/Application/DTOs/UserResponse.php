<?php

declare(strict_types=1);

namespace App\UserBC\Application\DTOs;

use App\UserBC\Domain\Aggregate\User;

final class UserResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly string $createdAt,
        public readonly bool $enabled
    ) {}

    public static function fromEntity(User $user): self
    {
        $personalData = $user->getPersonalData();

        return new self(
            $user->getId()->getValue(),
            $personalData->getName()->getFullName(),
            $personalData->getEmail()?->getValue(),
            $user->getCreatedAt()->getFormatted(),
            $user->isEnabled()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
        ];
    }
}
