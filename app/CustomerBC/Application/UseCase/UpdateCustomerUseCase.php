<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\CQRS\Command\UpdateCustomerCommand;
use App\CustomerBC\Application\DTO\CustomerResponse;
use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\CustomerBC\Domain\Aggregate\Customer;
use App\CustomerBC\Domain\Repository\CustomerFinderById;
use App\CustomerBC\Domain\Repository\CustomerDniFinder;
use App\CustomerBC\Domain\Repository\CustomerUpdater;
use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\SharedKernel\Domain\Ports\EncryptionService;

final class UpdateCustomerUseCase
{
    public function __construct(
        private readonly CustomerFinderById $finder,
        private readonly CustomerUpdater $updater,
        private readonly CustomerDniFinder $dniFinder,
        private readonly EncryptionService $encryption
    ) {}

    public function execute(UpdateCustomerCommand $command): array
    {
        $customerId = CustomerIdVO::fromString($command->id->getValue());
        $existingCustomer = $this->finder->findById($customerId);

        if (! $existingCustomer) {
            throw new CustomerNotFoundException;
        }

        $newDni = $command->personalData->getDni();
        $existingDni = $existingCustomer->getPersonalData()->getDni();

        if ($newDni->getNumber() !== $existingDni->getNumber() || 
            $newDni->getType() !== $existingDni->getType()) {
            if ($this->dniFinder->existsByDni(
                $newDni->getHash($this->encryption),
                $customerId->getValue()
            )) {
                throw new \App\CustomerBC\Application\Exceptions\CustomerAlreadyExistsException;
            }
        }

        $updatedCustomer = Customer::reconstitute(
            $customerId,
            $command->personalData,
            $existingCustomer->getCreatedAt(),
            $existingCustomer->isEnabled()
        );

        $this->updater->update($updatedCustomer);

        return CustomerResponse::fromEntity($updatedCustomer)->toArray();
    }
}