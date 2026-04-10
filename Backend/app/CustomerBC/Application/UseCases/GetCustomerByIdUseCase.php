<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\UseCases;

use App\CustomerBC\Application\DTOs\CustomerResponse;
use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\CustomerBC\Domain\Repositories\CustomerFinderById;
use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;

final class GetCustomerByIdUseCase
{
    public function __construct(
        private readonly CustomerFinderById $finder
    ) {}

    public function execute(string $id): CustomerResponse
    {
        $customerId = CustomerIdVO::fromString($id);

        $customer = $this->finder->findById($customerId);

        if (! $customer) {
            throw new CustomerNotFoundException;
        }

        return CustomerResponse::fromEntity($customer);
    }
}
