<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Persistence\Repository;

use App\PaymentBC\Domain\Aggregate\Payment;
use App\PaymentBC\Domain\Repository\PaymentFinderById;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;
use App\PaymentBC\Infrastructure\Persistence\Model\PaymentModel;
use App\PaymentBC\Infrastructure\Mapper\PaymentMapper;

final class EloquentPaymentFinderById implements PaymentFinderById
{
    public function __construct(
        private readonly PaymentMapper $mapper
    ) {}

    public function findById(PaymentIdVO $id): ?Payment
    {
        $model = PaymentModel::where('id', $id->getValue())->first();

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }
}