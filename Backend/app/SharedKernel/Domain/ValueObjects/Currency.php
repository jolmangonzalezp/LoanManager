<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

enum Currency: string
{
    case COP = 'COP';
    case USD = 'USD';
    case EUR = 'EUR';

    public function symbol(): string
    {
        return match ($this) {
            self::COP => '$',
            self::USD => 'US$',
            self::EUR => '€',
        };
    }

    public function decimalSeparator(): string
    {
        return match ($this) {
            self::COP => '.',
            self::USD, self::EUR => ',',
        };
    }

    public function thousandsSeparator(): string
    {
        return match ($this) {
            self::COP => '.',
            self::USD, self::EUR => ',',
        };
    }
}
