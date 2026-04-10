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
            throw new InvalidDniException('empty');
        }

        $length = strlen($number);

        if ($length < $type->minLength() || $length > $type->maxLength()) {
            throw new InvalidDniException('invalid_length');
        }

        if ($type->isNumeric() && ! ctype_digit($number)) {
            throw new InvalidDniException('invalid_format');
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
