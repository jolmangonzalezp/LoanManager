<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidNameException;

final class NameVO implements \Stringable
{
    private const NAME_PATTERN = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(?:\s[a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/';

    private const MIN_LENGTH = 2;

    private function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly ?string $middleName,
        private readonly string $secondLastName
    ) {}

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

        self::validate($firstName, $lastName, $secondLastName, $middleName);

        return new self($firstName, $lastName, $middleName, $secondLastName);
    }

    private static function sanitize(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    private static function validateName(string $name): void
    {
        $validators = [
            fn () => $name !== '' ?: throw new InvalidNameException('empty'),
            fn () => strlen($name) >= self::MIN_LENGTH ?: throw new InvalidNameException('too_short'),
            fn () => preg_match(self::NAME_PATTERN, $name) === 1 ?: throw new InvalidNameException('invalid_chars'),
        ];

        foreach ($validators as $validator) {
            $validator();
        }
    }

    private static function validate(
        string $firstName,
        string $lastName,
        string $secondLastName,
        ?string $middleName
    ): void {
        self::validateName($firstName);
        self::validateName($lastName);
        self::validateName($secondLastName);

        if ($middleName !== null) {
            self::validateName($middleName);
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

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
