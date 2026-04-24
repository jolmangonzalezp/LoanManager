<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\DTO;

use App\CustomerBC\Domain\Aggregate\Customer;
use App\SharedKernel\Domain\Ports\MaskingService;

final class CustomerSummaryResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $firstName,
        public readonly ?string $middleName,
        public readonly string $lastName,
        public readonly string $secondLastName
    ) {}

    public static function fromEntity(Customer $customer, ?MaskingService $masking = null): self
    {
        $name = $customer->getPersonalData()->getName();

        return new self(
            $customer->getId()->getValue(),
            $name->getFirstName(),
            $name->getMiddleName(),
            $name->getLastName(),
            $name->getSecondLastName()
        );
    }

    public function toArray(MaskingService $masking): array
    {
        return [
            'id' => $this->id,
            'name' => [
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'second_last_name' => $this->secondLastName
            ],
        ];
    }
}
