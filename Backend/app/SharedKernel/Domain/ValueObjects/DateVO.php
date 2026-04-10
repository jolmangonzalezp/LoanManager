<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use DateTimeInterface;

final class DateVO implements \Stringable
{
    private function __construct(
        private readonly DateTimeInterface $date
    ) {}

    public static function create(DateTimeInterface|string $date): self
    {
        if (is_string($date)) {
            $date = new \DateTimeImmutable($date);
        }

        return new self($date);
    }

    public static function today(): self
    {
        return new self(new \DateTimeImmutable('today'));
    }

    public static function now(): self
    {
        return new self(new \DateTimeImmutable('now'));
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getValue(): DateTimeInterface
    {
        return $this->date;
    }

    public function getYear(): int
    {
        return (int) $this->date->format('Y');
    }

    public function getMonth(): int
    {
        return (int) $this->date->format('n');
    }

    public function getDay(): int
    {
        return (int) $this->date->format('j');
    }

    public function getFormatted(string $format = 'Y-m-d'): string
    {
        return $this->date->format($format);
    }

    public function isAfter(self $other): bool
    {
        return $this->date > $other->date;
    }

    public function isBefore(self $other): bool
    {
        return $this->date < $other->date;
    }

    public function isSameDay(self $other): bool
    {
        return $this->date->format('Y-m-d') === $other->date->format('Y-m-d');
    }

    public function diffInDays(self $other): int
    {
        $interval = $other->date->diff($this->date);
        $sign = $interval->invert ? -1 : 1;

        return $sign * (int) $interval->format('%a');
    }

    public function addMonths(int $months): self
    {
        $newDateStr = $this->date->format('Y-m-d');
        $newDate = (new \DateTimeImmutable($newDateStr))->modify("+{$months} month");

        return new self($newDate);
    }

    public function isFuture(): bool
    {
        return $this->date > new \DateTimeImmutable;
    }

    public function isPast(): bool
    {
        return $this->date < new \DateTimeImmutable;
    }

    public function equals(self $other): bool
    {
        return $this->date->format('Y-m-d') === $other->date->format('Y-m-d');
    }

    public function __toString(): string
    {
        return $this->date->format('Y-m-d');
    }
}
