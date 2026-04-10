<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Repository;

use App\CustomerBC\Domain\Entities\Customer;
use App\CustomerBC\Domain\Repositories\CustomerUpdater as CustomerUpdaterInterface;
use App\CustomerBC\Infrastructure\Models\CustomerModel;
use App\CustomerBC\Infrastructure\Persistence\CustomerMapper;

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
