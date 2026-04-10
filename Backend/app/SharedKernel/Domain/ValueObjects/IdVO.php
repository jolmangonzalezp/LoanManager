<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

interface IdVO
{
    public static function fromString(string $value): static;

    public static function generate(): static;

    public function getValue(): string;
}
