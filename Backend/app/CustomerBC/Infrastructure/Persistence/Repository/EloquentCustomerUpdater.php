<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence\Repository;

use App\CustomerBC\Domain\Aggregate\Customer;
use App\CustomerBC\Domain\Repository\CustomerUpdater as CustomerUpdaterInterface;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\CustomerBC\Infrastructure\Mapper\CustomerMapper;

final class EloquentCustomerUpdater implements CustomerUpdaterInterface
{
    public function __construct(
        private readonly CustomerMapper $mapper
    ) {}

    public function update(Customer $customer): void
    {
        $data = $this->mapper->toPersistence($customer);

        CustomerModel::updateOrCreate(
            ['id' => $data['id']],
            $data
        );
    }
}
