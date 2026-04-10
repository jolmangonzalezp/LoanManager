<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCases;

use App\CustomerBC\Application\Commands\CreateCustomerCommand;
use App\CustomerBC\Application\DTOs\CustomerResponse;
use App\CustomerBC\Application\Exceptions\CustomerAlreadyExistsException;
use App\CustomerBC\Domain\Entities\Customer;
use App\CustomerBC\Domain\Repositories\CustomerCreator;
use App\CustomerBC\Domain\Repositories\CustomerDniFinder;

final class CreateCustomerUseCase
{
    public function __construct(
        private readonly CustomerCreator $creator,
        private readonly CustomerDniFinder $dniFinder
    ) {}

    public function execute(CreateCustomerCommand $command): CustomerResponse
    {
        $dni = $command->personalData->getDni();

        if ($this->dniFinder->existsByDni($dni->getNumber(), $dni->getType())) {
            throw new CustomerAlreadyExistsException;
        }

        $customer = Customer::create($command->personalData);

        $this->creator->create($customer);

        return CustomerResponse::fromEntity($customer);
    }
}
