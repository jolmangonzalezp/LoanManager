<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidDateException;
use DateTimeImmutable;
use DateTimeInterface;

final class DateVO
{
    private readonly DateTimeImmutable $value;

    private function __construct(
        DateTimeImmutable $date
    ) {
        $this->value = $date;
    }

    public static function fromString(string $value): self
    {
        if ($value === null || $value === '') {
            throw new InvalidDateException('La fecha no puede estar vacia');
        }

        if (!strtotime($value)) {
            throw new InvalidDateException('La fecha no es valida');
        }
        return new self(new \DateTimeImmutable($value));
    }

    public function fromDateTime(DateTimeInterface $dateTime): self
    {
        return new self($dateTime);
    }

    public function now(): self
    {
        return new self(new \DateTimeImmutable);
    }

    public function getValue(): DateTimeInterface
    {
        return $this->value;
    }

    public function getFormatted(): string
    {
        return $this->value->format('Y-m-d');
    }

    public function isAfter(self $other): bool
    {
        return $this->value > $other->value;
    }

    public function isBefore(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function isSameDay(self $other): bool
    {
        return $this->value->format('Y-m-d') === $other->value->format('Y-m-d');
    }

    public function addMonths(int $months = 1): self
    {
        $newDateStr = $this->value->format('Y-m-d');
        $newDate = (new \DateTimeImmutable($newDateStr))->modify("+{$months} month");

        return new self($newDate);
    }

    public function isFuture(): bool
    {
        return $this->value > new \DateTimeImmutable;
    }

    public function isPast(): bool
    {
        return $this->value < new \DateTimeImmutable;
    }

    public function equals(self $other): bool
    {
        return $this->value->format('Y-m-d') === $other->value->format('Y-m-d');
    }

    public function __toString(): string
    {
        return $this->value->format('Y-m-d');
    }
}
