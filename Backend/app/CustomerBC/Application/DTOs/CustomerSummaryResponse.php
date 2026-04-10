<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\DTOs;

final class CustomerSummaryResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name
    ) {}

    public static function fromEntity($customer): self
    {
        return new self(
            $customer->getId()->getValue(),
            $customer->getPersonalData()->getFullName()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
