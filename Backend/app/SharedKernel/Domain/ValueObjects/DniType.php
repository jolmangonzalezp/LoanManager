<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

enum DniType: string
{
    case CC = 'CC';
    case CE = 'CE';
    case NIT = 'NIT';
    case PASSPORT = 'PASSPORT';

    public function minLength(): int
    {
        return match ($this) {
            self::CC, self::PASSPORT, self::CE => 6,
            self::NIT => 9,
        };
    }

    public function maxLength(): int
    {
        return match ($this) {
            self::CC, self::NIT => 10,
            self::CE, self::PASSPORT => 15,
        };
    }

    public function isNumeric(): bool
    {
        return $this !== self::PASSPORT;
    }
}
