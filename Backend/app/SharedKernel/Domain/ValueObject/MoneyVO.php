<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use App\SharedKernel\Domain\Exception\InvalidMoneyException;

final class MoneyVO
{
    private readonly int $value;
    private function __construct(
        int $amount
    ) {
        $this->value = $amount;
    }

    public static function create(int $amount): self
    {
        if ($amount <= 0) {
            throw new InvalidMoneyException('Monto invalido');
        }

        return new self($amount);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function getAmount(): int
    {
        return $this->value;
    }

    public function add(self $other): self
    {
        return new self($this->value + $other->value);
    }

    public function subtract(self $other): self
    {

        $result = $this->value - $other->value;

        return new self(max(0, $result));
    }

    public function isZero(): bool
    {
        return $this->value === 0;
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->value > $other->value;
    }

    public function isLessThan(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
