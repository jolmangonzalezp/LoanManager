<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use App\SharedKernel\Domain\Exception\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;

abstract class UuidVO
{
    private readonly UuidInterface $uuid;
    private function __construct(
        UuidInterface $uuid
    ) {
        $this->uuid = $uuid;
    }

    public static function fromString(string $value): static
    {
        if (! RamseyUuid::isValid($value)) {
            throw new InvalidUuidException($value);
        }

        return new static(RamseyUuid::fromString($value));
    }

    public static function generate(): static
    {
        return new static(RamseyUuid::uuid7());
    }

    public function getValue(): string
    {
        return $this->uuid->toString();
    }

    public function equals(self $other): bool
    {
        return $other instanceof self && $this->uuid->equals($other->uuid);
    }
}
