<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Repository;

use App\CustomerBC\Domain\Entities\Customer;
use App\CustomerBC\Domain\Repositories\CustomerCreator as CustomerCreatorInterface;
use App\CustomerBC\Infrastructure\Models\CustomerModel;
use App\CustomerBC\Infrastructure\Persistence\CustomerMapper;

final class EloquentCustomerCreator implements CustomerCreatorInterface
{
    public function __construct(
        private readonly CustomerMapper $mapper
    ) {}

    public function create(Customer $customer): void
    {
        $data = $this->mapper->toPersistence($customer);

        CustomerModel::create($data);
    }
}
