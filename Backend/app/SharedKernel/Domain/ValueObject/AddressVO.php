<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use App\SharedKernel\Domain\Exception\InvalidAddressException;

final class AddressVO
{
    private readonly string $value;

    private function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public static function create(string $address): self
    {
        $address = trim($address);

        if ($address === '') {
            throw new InvalidAddressException('La dirección es requerida');
        }

        if (strlen($address) < 10) {
            throw new InvalidAddressException('La dirección invalida: La dirección es muy corta');
        }

        return new self($address);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
