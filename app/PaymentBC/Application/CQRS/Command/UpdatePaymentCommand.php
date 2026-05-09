<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\CQRS\Command;

use App\PaymentBC\Domain\ValueObject\LoanIdVO;
use App\PaymentBC\Domain\ValueObject\PaymentIdVO;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final readonly class UpdatePaymentCommand
{
    public function __construct(
        public PaymentIdVO $paymentId,
        public LoanIdVO $loanId,
        public MoneyVO $amount,
        public ?DateVO $paymentDate = null
    ) {}
}
