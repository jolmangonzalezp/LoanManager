<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\DTO;

final class CreatedCustomerResponse
{
    public function __construct(
        public readonly string $id
    ) {}

    public static function fromEntity($customer): self
    {
        return new self($customer->getId()->getValue());
    }

    public function toArray(): array
    {
        return ['id' => $this->id];
    }
}