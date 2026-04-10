<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Repository;

use App\CustomerBC\Domain\Entities\Customer;
use App\CustomerBC\Domain\Repositories\CustomerFinderById as CustomerFinderByIdInterface;
use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;
use App\CustomerBC\Infrastructure\Models\CustomerModel;
use App\CustomerBC\Infrastructure\Persistence\CustomerMapper;

final class EloquentCustomerFinderById implements CustomerFinderByIdInterface
{
    public function __construct(
        private readonly CustomerMapper $mapper
    ) {}

    public function findById(CustomerIdVO $id): ?Customer
    {
        $model = CustomerModel::find($id->getValue());

        return $model ? $this->mapper->toDomain($model) : null;
    }
}
