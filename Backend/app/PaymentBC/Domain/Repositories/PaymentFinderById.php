<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Repositories;

use App\PaymentBC\Domain\Entities\Payment;
use App\PaymentBC\Domain\ValueObjects\PaymentIdVO;

interface PaymentFinderById
{
    public function findById(PaymentIdVO $id): ?Payment;
}
