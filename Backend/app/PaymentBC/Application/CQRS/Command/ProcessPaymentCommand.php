<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\CQRS\Command;

use App\LoanBC\Domain\ValueObject\LoanIdVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final class ProcessPaymentCommand
{
    public function __construct(
        public readonly LoanIdVO $loanId,
        public readonly MoneyVO $amount,
        public readonly ?string $paymentDate = null
    ) {}
}
