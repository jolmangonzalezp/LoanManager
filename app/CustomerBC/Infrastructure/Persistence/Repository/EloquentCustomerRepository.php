<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence\Repository;

use App\CustomerBC\Domain\Repository\FindActiveCustomers;
use App\CustomerBC\Domain\Repository\FindActiveCustomersByIds;
use App\CustomerBC\Infrastructure\Mapper\CustomerMapper;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;

final class EloquentCustomerRepository implements FindActiveCustomers, FindActiveCustomersByIds
{
    public function __construct(
        private readonly CustomerMapper $mapper
    ) {}

    public function findActive(): array
    {
        $models = CustomerModel::where('enabled', true)->get();

        return $models->map(fn ($m) => $this->mapper->toDomain($m))->toArray();
    }

    public function findActiveByIds(array $ids): array
    {
        $models = CustomerModel::whereIn('id', $ids)
            ->where('enabled', true)
            ->get();

        return $models->map(fn ($m) => $this->mapper->toDomain($m))->toArray();
    }
}
