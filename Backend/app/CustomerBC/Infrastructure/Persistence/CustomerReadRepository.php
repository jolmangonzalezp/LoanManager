<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence;

use App\CustomerBC\Domain\Entities\Customer;
use App\CustomerBC\Domain\Repositories\CustomerReadRepository as CustomerReadRepositoryInterface;
use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;
use App\CustomerBC\Infrastructure\Models\CustomerModel;

final class CustomerReadRepository implements CustomerReadRepositoryInterface
{
    public function __construct(
        private readonly CustomerMapper $mapper
    ) {}

    public function findById(CustomerIdVO $id): ?Customer
    {
        $model = CustomerModel::find($id->getValue());

        return $model ? $this->mapper->toDomain($model) : null;
    }

    public function findAll(): array
    {
        return CustomerModel::all()
            ->map(fn ($model) => $this->mapper->toDomain($model))
            ->all();
    }

    public function exists(CustomerIdVO $id): bool
    {
        return CustomerModel::where('id', $id->getValue())->exists();
    }
}
