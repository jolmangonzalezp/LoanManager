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
        $nameParts = explode(' ', $this->name, 4);
        
        $dniFormatted = $this->dni;
        $dniType = 'CC';
        if (preg_match('/^([A-Z]+)[.:]?(.+)$/', $dniFormatted, $matches)) {
            $dniType = $matches[1];
            $dniFormatted = $matches[2];
        }
        
        return [
            'id' => $this->id,
            'name' => [
                'first_name' => $nameParts[0] ?? '',
                'last_name' => $nameParts[1] ?? '',
                'second_last_name' => $nameParts[2] ?? '',
                'third_last_name' => $nameParts[3] ?? ''
            ],
            'dni' => [
                'type' => $dniType,
                'number' => trim($dniFormatted)
            ],
            'phone' => $this->phone,
            'address' => $this->address,
            'email' => $this->email,
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
        ];
    }
}
