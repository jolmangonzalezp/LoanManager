<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Domain\Repository\FindActiveCustomers;
use App\CustomerBC\Domain\Repository\FindActiveCustomersByIds;

final class GetCustomerNamesUseCase
{
    public function __construct(
        private readonly FindActiveCustomers $allActive,
        private readonly FindActiveCustomersByIds $activeByIds
    ) {}

    public function execute(?array $customerIds = null): array
    {
        $customers = ($customerIds === null) 
            ? $this->allActive->findActive()
            : $this->activeByIds->findActiveByIds($customerIds);

        return array_map(fn($c) => [
            'id' => $c->getId()->getValue(),
            'full_name' => $c->getPersonalData()->getName()->getShortName()
        ], $customers);
    }
}
