<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Persistence\Repository;

use App\PaymentBC\Domain\Aggregate\Payment;
use App\PaymentBC\Domain\Repository\PaymentUpdater;
use App\PaymentBC\Infrastructure\Mapper\PaymentMapper;
use App\PaymentBC\Infrastructure\Persistence\Model\PaymentModel;

final readonly class EloquentPaymentUpdater implements PaymentUpdater
{
    public function __construct(
        private readonly PaymentMapper $mapper
    ) {}

    public function update(Payment $payment): void
    {
        $data = $this->mapper->toPersistence($payment);

        PaymentModel::where('id', $payment->getId()->getValue())->update($data);
    }
}
