<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use App\SharedKernel\Domain\Exception\InvalidNameException;

final class NameVO
{
    private readonly string $firstName;
    private readonly ?string $middleName;
    private readonly string $lastName;
    private readonly string $secondLastName;

    private function __construct(
        string $firstName,
        ?string $middleName,
        string $lastName,
        string $secondLastName
    ) {
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->secondLastName = $secondLastName;
    }

    public static function create(
        string $firstName,
        string $lastName,
        string $secondLastName,
        ?string $middleName = null
    ): self {

        $firstName = self::sanitize($firstName);
        $lastName = self::sanitize($lastName);
        $secondLastName = self::sanitize($secondLastName);
        $middleName = $middleName !== null ? self::sanitize($middleName) : null;

        self::validate($firstName);
        self::validate($lastName);
        self::validate($secondLastName);
        self::validate($middleName !== null ? $middleName : '', false);
        return new self($firstName, $middleName, $lastName, $secondLastName);
    }

    private static function sanitize(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    private static function validate(
        string $value,
        bool $isRequired = true
    ): void {
        if ($isRequired && $value === '') {
            throw new InvalidNameException('Nombre o apellido es requerido');
        }
        if ($isRequired && mb_strlen($value) < 3) {
            throw new InvalidNameException('Nombre o apellido debe tener al menos 3 caracteres');
        }

        if ($value !== '' && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(?:\s[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/', $value)) {
            throw new InvalidNameException('Nombre o apellido contiene caracteres inválidos');
        }
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getSecondLastName(): string
    {
        return $this->secondLastName;
    }

    public function getFullName(): string
    {
        $parts = array_filter([
            $this->firstName,
            $this->middleName,
            $this->lastName,
            $this->secondLastName,
        ]);

        return implode(' ', $parts);
    }

    public function getShortName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function equals(self $other): bool
    {
        return $this->firstName === $other->firstName
            && $this->lastName === $other->lastName
            && $this->middleName === $other->middleName
            && $this->secondLastName === $other->secondLastName;
    }
}
