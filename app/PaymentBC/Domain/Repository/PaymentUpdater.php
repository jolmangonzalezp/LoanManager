<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Repository;

use App\PaymentBC\Domain\Aggregate\Payment;

interface PaymentUpdater
{
    public function update(Payment $payment): void;
}
