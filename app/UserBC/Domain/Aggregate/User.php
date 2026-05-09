<?php

declare(strict_types=1);

namespace App\UserBC\Domain\Aggregate;

use App\SharedKernel\Domain\Exception\DomainException;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;
use App\UserBC\Domain\ValueObject\UserIdVO;
use Illuminate\Support\Facades\Hash;

final class User
{
    private function __construct(
        private readonly UserIdVO $id,
        private readonly string $username,
        private readonly string $password,
        private readonly bool $enabled,
        private readonly DateVO $createdAt,
        private readonly ?NameVO $name,
        private readonly ?EmailVO $email,
        private readonly ?PhoneVO $phone,
        private readonly ?string $rememberToken,
    ) {}

    public static function create(
        string $username,
        string $password,
        ?NameVO $name = null,
        ?EmailVO $email = null,
        ?PhoneVO $phone = null,
    ): self {
        if (empty($username) || strlen($username) < 3) {
            throw new DomainException('invalid_username', 'El usuario debe tener al menos 3 caracteres');
        }

        if (empty($password) || strlen($password) < 6) {
            throw new DomainException('weak_password', 'La contraseña debe tener al menos 6 caracteres');
        }

        return new self(
            UserIdVO::generate(),
            $username,
            Hash::make($password),
            true,
            DateVO::now(),
            $name,
            $email,
            $phone,
            null,
        );
    }

    public static function reconstitute(
        UserIdVO $id,
        string $username,
        string $password,
        bool $enabled,
        DateVO $createdAt,
        ?NameVO $name = null,
        ?EmailVO $email = null,
        ?PhoneVO $phone = null,
        ?string $rememberToken = null,
    ): self {
        return new self(
            $id,
            $username,
            $password,
            $enabled,
            $createdAt,
            $name,
            $email,
            $phone,
            $rememberToken,
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
            $this->username,
            $this->password,
            $this->enabled,
            $this->createdAt,
            $this->name,
            $this->email,
            $this->phone,
            $token,
        );
    }

    public function enable(): self
    {
        return new self(
            $this->id,
            $this->username,
            $this->password,
            true,
            $this->createdAt,
            $this->name,
            $this->email,
            $this->phone,
            $this->rememberToken,
        );
    }

    public function disable(): self
    {
        return new self(
            $this->id,
            $this->username,
            $this->password,
            false,
            $this->createdAt,
            $this->name,
            $this->email,
            $this->phone,
            $this->rememberToken,
        );
    }

    public function getId(): UserIdVO
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getCreatedAt(): DateVO
    {
        return $this->createdAt;
    }

    public function getName(): ?NameVO
    {
        return $this->name;
    }

    public function getEmail(): ?EmailVO
    {
        return $this->email;
    }

    public function getPhone(): ?PhoneVO
    {
        return $this->phone;
    }

    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }
}
