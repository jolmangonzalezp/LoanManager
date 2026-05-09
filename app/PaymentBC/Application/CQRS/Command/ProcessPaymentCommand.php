<?php

declare(strict_types=1);

namespace App\PaymentBC\Application\CQRS\Command;

use App\PaymentBC\Domain\ValueObject\LoanIdVO;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

final readonly class ProcessPaymentCommand
{
    public function __construct(
        public LoanIdVO $loanId,
        public MoneyVO $amount,
        public ?DateVO $paymentDate
    ) {}

    public function getLoanId(): LoanIdVO
    {
        return $this->loanId;
    }

    public function getAmount(): MoneyVO
    {
        return $this->amount;
    }

    public function getPaymentDate(): ?DateVO
    {
        return $this->paymentDate;
    }
}
