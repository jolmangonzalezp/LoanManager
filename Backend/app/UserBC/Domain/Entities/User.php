<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Entities;

use App\SharedKernel\Domain\Exceptions\DomainException;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\PersonVO;
use App\UserBC\Domain\ValueObjects\UserIdVO;
use Illuminate\Support\Facades\Hash;

final class User
{
    private function __construct(
        private readonly UserIdVO $id,
        private readonly PersonVO $personalData,
        private readonly string $password,
        private readonly ?string $rememberToken,
        private readonly DateVO $emailVerifiedAt,
        private readonly DateVO $createdAt,
        private readonly bool $enabled
    ) {}

    public static function create(
        PersonVO $personalData,
        string $password
    ): self {
        if (empty($password) || strlen($password) < 6) {
            throw new DomainException('weak_password', 'La contraseña debe tener al menos 6 caracteres');
        }

        return new self(
            UserIdVO::generate(),
            $personalData,
            Hash::make($password),
            null,
            null,
            DateVO::now(),
            true
        );
    }

    public static function reconstitute(
        UserIdVO $id,
        PersonVO $personalData,
        string $password,
        ?string $rememberToken,
        ?DateVO $emailVerifiedAt,
        DateVO $createdAt,
        bool $enabled
    ): self {
        return new self(
            $id,
            $personalData,
            $password,
            $rememberToken,
            $emailVerifiedAt,
            $createdAt,
            $enabled
        );
    }

    public function verifyPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function setRememberToken(string $token): self
    {
        return new self(
            $this->id,
            $this->personalData,
            $this->password,
            $token,
            $this->emailVerifiedAt,
            $this->createdAt,
            $this->enabled
        );
    }

    public function enable(): self
    {
        return new self(
            $this->id,
            $this->personalData,
            $this->password,
            $this->rememberToken,
            $this->emailVerifiedAt,
            $this->createdAt,
            true
        );
    }

    public function disable(): self
    {
        return new self(
            $this->id,
            $this->personalData,
            $this->password,
            $this->rememberToken,
            $this->emailVerifiedAt,
            $this->createdAt,
            false
        );
    }

    public function getId(): UserIdVO
    {
        return $this->id;
    }

    public function getPersonalData(): PersonVO
    {
        return $this->personalData;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    public function getEmailVerifiedAt(): ?DateVO
    {
        return $this->emailVerifiedAt;
    }

    public function getCreatedAt(): DateVO
    {
        return $this->createdAt;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }
}
