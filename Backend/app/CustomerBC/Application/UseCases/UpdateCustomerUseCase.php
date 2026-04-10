<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCases;

use App\CustomerBC\Application\Commands\UpdateCustomerCommand;
use App\CustomerBC\Application\DTOs\CustomerResponse;
use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\CustomerBC\Domain\Entities\Customer;
use App\CustomerBC\Domain\Repositories\CustomerFinderById;
use App\CustomerBC\Domain\Repositories\CustomerUpdater;
use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;

final class UpdateCustomerUseCase
{
    public function __construct(
        private readonly CustomerFinderById $finder,
        private readonly CustomerUpdater $updater
    ) {}

    public function execute(UpdateCustomerCommand $command): CustomerResponse
    {
        $customerId = CustomerIdVO::fromString($command->id);
        $existingCustomer = $this->finder->findById($customerId);

        if (! $existingCustomer) {
            throw new CustomerNotFoundException;
        }

        $updatedCustomer = Customer::reconstitute(
            $customerId,
            $command->personalData,
            $existingCustomer->getCreatedAt(),
            $existingCustomer->isEnabled()
        );

        $this->updater->update($updatedCustomer);

        return CustomerResponse::fromEntity($updatedCustomer);
    }
}
