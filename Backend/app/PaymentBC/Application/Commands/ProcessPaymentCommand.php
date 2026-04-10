<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\Commands;

use App\LoanBC\Domain\ValueObjects\LoanIdVO;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

final class ProcessPaymentCommand
{
    public function __construct(
        public readonly LoanIdVO $loanId,
        public readonly MoneyVO $amount,
        public readonly ?string $paymentDate = null
    ) {}
}
