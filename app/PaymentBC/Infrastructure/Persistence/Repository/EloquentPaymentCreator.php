<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Persistence\Repository;

use App\PaymentBC\Domain\Aggregate\Payment;
use App\PaymentBC\Domain\Repository\PaymentCreator;
use App\PaymentBC\Infrastructure\Mapper\PaymentMapper;
use App\PaymentBC\Infrastructure\Persistence\Model\PaymentModel;

final class EloquentPaymentCreator implements PaymentCreator
{
    public function __construct(
        private readonly PaymentMapper $mapper
    ) {}

    public function create(Payment $payment): void
    {
        $data = $this->mapper->toPersistence($payment);

        PaymentModel::create($data);
    }
}
