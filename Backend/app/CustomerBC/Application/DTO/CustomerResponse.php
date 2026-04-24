<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\DTO;

use App\CustomerBC\Domain\Aggregate\Customer;
use App\SharedKernel\Domain\Ports\MaskingService;

final class CustomerResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $firstName,
        public readonly ?string $middleName,
        public readonly string $lastName,
        public readonly string $secondLastName,
        public readonly string $dniNumber,
        public readonly string $dniType,
        public readonly string $phone,
        public readonly string $address,
        public readonly ?string $email,
        public readonly string $createdAt,
        public readonly bool $enabled
    ) {}

    public static function fromEntity(Customer $customer): self
    {
        $person = $customer->getPersonalData();
        $name = $person->getName();
        $dni = $person->getDni();
        $phone = $person->getPhone();

        return new self(
            $customer->getId()->getValue(),
            $name->getFirstName(),
            $name->getMiddleName(),
            $name->getLastName(),
            $name->getSecondLastName(),
            $dni->getNumber(),
            $dni->getType()->value,
            $phone->getNumber(),
            $person->getAddress()->getValue(),
            $person->getEmail()?->getValue(),
            $customer->getCreatedAt()->getFormatted(),
            $customer->isEnabled()
        );
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            'name' => [
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'second_last_name' => $this->secondLastName,
            ],
            'dni' => [
                'type' => $this->dniType,
                'number' => $this->dniNumber,
            ],
            'phone' => $this->phone,
            'address' => $this->address,
            'email' => $this->email,
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
        ];
    }

    private function buildFullName(): string
    {
        $parts = array_filter([
            $this->firstName,
            $this->middleName,
            $this->lastName,
            $this->secondLastName,
        ]);

        return implode(' ', $parts);
    }

    public function toArrayMasked(MaskingService $masking): array
    {
        $fullName = $this->buildFullName();
        $partialName = $this->firstName . ' ' . $this->lastName;

        return [
            'id' => $this->id,
            'first_name' => $masking->mask($this->firstName),
            'middle_name' => $this->middleName ? $masking->mask($this->middleName) : null,
            'last_name' => $masking->mask($this->lastName),
            'second_last_name' => $masking->mask($this->secondLastName),
            'full_name' => $masking->mask($fullName),
            'partial_name' => $masking->mask($partialName),
            'name' => [
                'first_name' => $masking->mask($this->firstName),
                'middle_name' => $this->middleName ? $masking->mask($this->middleName) : null,
                'last_name' => $masking->mask($this->lastName),
                'second_last_name' => $masking->mask($this->secondLastName),
            ],
            'dni' => [
                'type' => $this->dniType,
                'number' => $masking->maskEnd($this->dniNumber),
            ],
            'full_dni' => $masking->maskEnd($this->dniNumber),
            'phone' => $masking->maskEnd($this->phone),
            'address' => $masking->mask($this->address),
            'email' => $this->email ? $masking->mask($this->email) : null,
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
        ];
    }
}
