<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidDniException;

final class DniVO implements \Stringable
{
    private function __construct(
        private readonly string $number,
        private readonly DniType $type
    ) {}

    public static function create(string $number, DniType $type = DniType::CC): self
    {
        $value = trim($number);
        
        $number = $type->isNumeric()
            ? preg_replace('/[^0-9]/', '', trim($number))
            : preg_replace('/[^A-Za-z0-9]/', '', trim($number));

        $number = self::sanitize($number);

        self::validate($number, $type);

        return new self($number, $type);
    }

    private static function sanitize(string $value): string
    {
        return strtoupper(trim($value));
    }

    private static function validate(string $number, DniType $type): void
    {
        if ($number === '') {
            throw new InvalidDniException('El número de identificación es requerido');
        }

        $length = strlen($number);

        if ($length < $type->minLength() || $length > $type->maxLength()) {
            throw new InvalidDniException('El número de identificación tiene una longitud inválida');
        }

        if ($type === DniType::PASSPORT) {
            if (!preg_match('/^[A-Z0-9]+$/', $number)) {
                throw new InvalidDniException('Formato de pasaporte inválido');
            }
        }

        if (!ctype_digit($number)) {
            throw new InvalidDniException('El número de identificación debe contener solo dígitos');
        }
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getType(): DniType
    {
        return $this->type;
    }

    public function getFormatted(): string
    {
        return match ($this->type) {
            DniType::CC, DniType::NIT => number_format((int) $this->number, 0, '', '.'),
            default => $this->number
        };
    }

    public function equals(self $other): bool
    {
        return $this->number === $other->number && $this->type === $other->type;
    }

    public function __toString(): string
    {
        return "{$this->type->value}:{$this->number}";
    }
}
