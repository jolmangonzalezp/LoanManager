<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence;

use App\CustomerBC\Domain\Repositories\CustomerFinderAll as CustomerFinderAllInterface;
use App\CustomerBC\Infrastructure\Models\CustomerModel;

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
