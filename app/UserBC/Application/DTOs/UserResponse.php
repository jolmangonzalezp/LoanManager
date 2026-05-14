<?php

declare(strict_types=1);

namespace App\UserBC\Application\DTOs;

use App\UserBC\Domain\Aggregate\User;

final class UserResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly ?string $firstName,
        public readonly ?string $middleName,
        public readonly ?string $lastName,
        public readonly ?string $secondLastName,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly string $createdAt,
        public readonly bool $enabled,
    ) {}

    public static function fromEntity(User $user): self
    {
        $name = $user->getName();

        return new self(
            $user->getId()->getValue(),
            $user->getUsername(),
            $name?->getFirstName(),
            $name?->getMiddleName(),
            $name?->getLastName(),
            $name?->getSecondLastName(),
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
            'name' => [
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'second_last_name' => $this->secondLastName,
            ],
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
        ];
    }
}
