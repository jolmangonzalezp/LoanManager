<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence\Repository;

use App\CustomerBC\Domain\Aggregate\Customer;
use App\CustomerBC\Domain\Repository\CustomerFinderById as CustomerFinderByIdInterface;
use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\CustomerBC\Infrastructure\Mapper\CustomerMapper;

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
