<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Repository;

use App\PaymentBC\Domain\Aggregate\Payment;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;

interface PaymentFinderById
{
    public function findById(PaymentIdVO $id): ?Payment;
}
