<?php

declare(strict_types=1);

namespace App\PaymentBC\Infrastructure\Repositories;

use App\PaymentBC\Domain\Entities\Payment;
use App\PaymentBC\Domain\Repositories\PaymentCreator;
use App\PaymentBC\Infrastructure\Models\PaymentModel;

final class EloquentPaymentCreator implements PaymentCreator
{
    public function create(Payment $payment): void
    {
        $mapper = new PaymentMapper;
        $data = $mapper->toPersistence($payment);

        PaymentModel::create($data);
    }
}
