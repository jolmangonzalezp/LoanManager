<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Repositories;

use App\PaymentBC\Domain\Entities\Payment;

interface PaymentCreator
{
    public function create(Payment $payment): void;
}
