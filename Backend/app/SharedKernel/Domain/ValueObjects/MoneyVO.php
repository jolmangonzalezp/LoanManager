<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidMoneyException;

final class MoneyVO implements \Stringable
{
    private function __construct(
        private readonly int $amount,
        private readonly Currency $currency
    ) {}

    public static function create(int $amount, Currency $currency = Currency::COP): self
    {
        if ($amount <= 0) {
            throw new InvalidMoneyException('negative');
        }

        return new self($amount, $currency);
    }

    public static function zero(Currency $currency = Currency::COP): self
    {
        return new self(0, $currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getFormatted(): string
    {
        return $this->currency->symbol().number_format(
            $this->amount,
            0,
            $this->currency->decimalSeparator(),
            $this->currency->thousandsSeparator()
        );
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidMoneyException('currency_mismatch');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidMoneyException('currency_mismatch');
        }

        $result = $this->amount - $other->amount;

        return new self(max(0, $result), $this->currency);
    }

    public function multiply(int $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->amount > $other->amount;
    }

    public function isLessThan(self $other): bool
    {
        return $this->amount < $other->amount;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function __toString(): string
    {
        return $this->getFormatted();
    }
}
