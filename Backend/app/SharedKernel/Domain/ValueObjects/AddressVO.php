<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidAddressException;

final class AddressVO implements \Stringable
{
    private function __construct(
        private readonly string $value
    ) {}

    public static function create(string $address): self
    {
        $address = trim($address);

        if ($address === '') {
            throw new InvalidAddressException('empty');
        }

        if (strlen($address) < 10) {
            throw new InvalidAddressException('too_short');
        }

        return new self($address);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return strcasecmp($this->value, $other->value) === 0;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
