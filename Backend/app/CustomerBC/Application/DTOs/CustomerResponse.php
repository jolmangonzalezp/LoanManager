<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\DTOs;

final class CustomerResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $dni,
        public readonly string $phone,
        public readonly string $address,
        public readonly ?string $email,
        public readonly string $createdAt,
        public readonly bool $enabled
    ) {}

    public static function fromEntity($customer): self
    {
        return new self(
            $customer->getId()->getValue(),
            $customer->getPersonalData()->getFullName(),
            $customer->getPersonalData()->getDni()->getFormatted(),
            $customer->getPersonalData()->getPhone()->getInternationalFormat(),
            $customer->getPersonalData()->getAddress()->getValue(),
            $customer->getPersonalData()->getEmail()?->getValue(),
            $customer->getCreatedAt()->getFormatted('Y-m-d H:i:s'),
            $customer->isEnabled()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dni' => $this->dni,
            'phone' => $this->phone,
            'address' => $this->address,
            'email' => $this->email,
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
        ];
    }
}
