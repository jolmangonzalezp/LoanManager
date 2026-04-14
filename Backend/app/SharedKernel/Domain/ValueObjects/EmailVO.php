<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidEmailException;

final class EmailVO
{
    private readonly string $value;
    private function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public static function create(string $email): self
    {
        $value = strtolower(trim($email));

        self::validate($email);

        return new self($value);
    }

    private static function validate(string $email): void
    {
        if ($email === null || $email === '') {
            throw new InvalidEmailException('El email es requerido.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException('El email proporcionado no es válido.');
        }

    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
