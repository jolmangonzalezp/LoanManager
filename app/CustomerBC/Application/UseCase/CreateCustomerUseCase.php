<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCase;

use App\CustomerBC\Application\CQRS\Command\CreateCustomerCommand;
use App\CustomerBC\Application\DTO\CreatedCustomerResponse;
use App\CustomerBC\Application\Exceptions\CustomerAlreadyExistsException;
use App\CustomerBC\Domain\Aggregate\Customer;
use App\CustomerBC\Domain\Repository\CustomerCreator;
use App\CustomerBC\Domain\Repository\CustomerDniFinder;

final class CreateCustomerUseCase
{
    private ?CreatedCustomerResponse $response = null;

    public function __construct(
        private readonly CustomerCreator $creator,
        private readonly CustomerDniFinder $dniFinder
    ) {}

    public function getResponse(): ?CreatedCustomerResponse
    {
        return $this->response;
    }

    public function execute(CreateCustomerCommand $command): bool
    {
        $dni = $command->personalData->getDni();

        if ($this->dniFinder->existsByDni($dni->getHash())) {
            throw new CustomerAlreadyExistsException;
        }

        $customer = Customer::create($command->personalData);

        $this->creator->create($customer);

        $this->response = CreatedCustomerResponse::fromEntity($customer);

        return true;
    }
}
