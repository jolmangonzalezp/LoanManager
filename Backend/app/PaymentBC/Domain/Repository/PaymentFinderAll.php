<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Repository;

use App\PaymentBC\Domain\Aggregate\Payment;

interface PaymentFinderAll
{
    /** @return array<Payment> */
    public function findAll(): array;
}
