<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;

final class UuidVO
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

        return new self(RamseyUuid::fromString($value));
    }

    public static function generate(): static
    {
        return new self(RamseyUuid::uuid7());
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
