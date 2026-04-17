<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence\Repository;

use App\CustomerBC\Domain\Aggregate\Customer;
use App\CustomerBC\Domain\Repository\CustomerCreator as CustomerCreatorInterface;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\CustomerBC\Infrastructure\Mapper\CustomerMapper;

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
