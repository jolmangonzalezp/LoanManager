<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence\Repository;

use App\CustomerBC\Domain\Repository\CustomerFinderAll as CustomerFinderAllInterface;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\CustomerBC\Infrastructure\Mapper\CustomerMapper;

final class EloquentCustomerFinderAll implements CustomerFinderAllInterface
{
    public function __construct(
        private readonly CustomerMapper $mapper
    ) {}

    public function findAll(): array
    {
        return CustomerModel::all()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->all();
    }
}
