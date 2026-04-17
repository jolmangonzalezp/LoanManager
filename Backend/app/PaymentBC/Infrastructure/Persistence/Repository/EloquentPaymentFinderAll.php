<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Persistence\Repository;

use App\PaymentBC\Domain\Repository\PaymentFinderAll;
use App\PaymentBC\Infrastructure\Mapper\PaymentMapper;
use App\PaymentBC\Infrastructure\Persistence\Model\PaymentModel;

final class EloquentPaymentFinderAll implements PaymentFinderAll
{
    public function __construct(
        private readonly PaymentMapper $mapper
    ) {}

    public function findAll(): array
    {
        $models = PaymentModel::orderBy('created_at', 'desc')->get();

        return $models->map(fn ($m) => $this->mapper->toDomain($m))->toArray();
    }
}
