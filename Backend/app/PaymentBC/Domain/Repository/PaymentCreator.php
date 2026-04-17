<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Repository;

use App\PaymentBC\Domain\Aggregate\Payment;

interface PaymentCreator
{
    public function create(Payment $payment): void;
}
